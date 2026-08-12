<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PembayaranModel;
use App\Models\PiutangModel;
use CodeIgniter\Database\BaseConnection;
use RuntimeException;
use Throwable;

/**
 * --------------------------------------------------------------------------
 * Payment Service
 * --------------------------------------------------------------------------
 *
 * Business logic transaksi pembayaran piutang.
 *
 * Tanggung jawab:
 *
 * - Mengambil data piutang
 * - Menghitung denda pada tanggal pembayaran
 * - Menghitung total tagihan
 * - Menghitung pembayaran yang sudah dilakukan
 * - Menghitung sisa masing-masing komponen
 * - Mengalokasikan pembayaran:
 *
 *      Denda → Bunga → Pokok
 *
 * - Membuat transaksi pembayaran
 * - Membatalkan transaksi pembayaran
 *
 * Catatan:
 *
 * Formula denda TIDAK dihitung ulang di Service.
 *
 * Satu-satunya sumber formula denda adalah:
 *
 *      PiutangModel::calculatePenalty()
 *
 * Hal ini mencegah terjadinya perbedaan formula antara
 * modul Piutang dan modul Pembayaran.
 */
class PaymentService
{
    /**
     * Model Piutang.
     */
    protected PiutangModel $piutangModel;

    /**
     * Model Pembayaran.
     */
    protected PembayaranModel $pembayaranModel;

    /**
     * Database connection.
     */
    protected BaseConnection $db;


    /**
     * ----------------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------------
     */
    public function __construct(
        ?PiutangModel $piutangModel = null,
        ?PembayaranModel $pembayaranModel = null,
        ?BaseConnection $db = null
    ) {
        $this->piutangModel =
            $piutangModel ?? new PiutangModel();

        $this->pembayaranModel =
            $pembayaranModel ?? new PembayaranModel();

        $this->db =
            $db ?? db_connect();
    }


    /**
     * ----------------------------------------------------------------------
     * Preview / Calculate Payment
     * ----------------------------------------------------------------------
     *
     * Menghitung kondisi tagihan pada tanggal pembayaran.
     *
     * Method ini TIDAK menyimpan transaksi.
     *
     * Digunakan untuk:
     *
     * - Preview form pembayaran
     * - Validasi
     * - Informasi tagihan
     *
     * @throws RuntimeException
     */
    public function calculatePayment(
        int $piutangId,
        string $tanggalPembayaran
    ): array {
        $piutang =
            $this->getPiutang($piutangId);

        /*
         * Validasi tanggal pembayaran.
         */
        $this->validatePaymentDate(
            $piutang,
            $tanggalPembayaran
        );

        /*
         * --------------------------------------------------------------
         * Nilai dasar piutang
         * --------------------------------------------------------------
         */

        $nominalPokok =
            (float) $piutang['nominal_pokok'];

        $nominalBunga =
            (float) $piutang['nominal_bunga'];

        /*
         * --------------------------------------------------------------
         * Denda
         * --------------------------------------------------------------
         *
         * Formula denda menggunakan PiutangModel sebagai
         * single source of truth.
         *
         * Parameter berasal dari snapshot yang tersimpan
         * pada Piutang.
         */

        $dendaBerjalan =
            $this->calculatePenalty(
                $piutang,
                $tanggalPembayaran
            );

        /*
         * --------------------------------------------------------------
         * Pembayaran VALID sebelumnya
         * --------------------------------------------------------------
         *
         * Pembayaran DIBATALKAN tidak ikut dihitung.
         */

        $pembayaranSebelumnya =
            $this->getPreviousAllocation(
                $piutangId
            );

        /*
         * --------------------------------------------------------------
         * Sisa masing-masing komponen
         * --------------------------------------------------------------
         */

        $sisaDenda = max(
            0.0,
            $dendaBerjalan
            - $pembayaranSebelumnya['denda']
        );

        $sisaBunga = max(
            0.0,
            $nominalBunga
            - $pembayaranSebelumnya['bunga']
        );

        $sisaPokok = max(
            0.0,
            $nominalPokok
            - $pembayaranSebelumnya['pokok']
        );

        /*
         * --------------------------------------------------------------
         * Total tagihan berjalan
         * --------------------------------------------------------------
         */

        $totalTagihan =
            $sisaDenda
            + $sisaBunga
            + $sisaPokok;

        $totalTagihan =
            $this->money($totalTagihan);

        return [
            'piutang_id' =>
                $piutangId,

            'tanggal_pembayaran' =>
                $tanggalPembayaran,

            /*
             * Data dasar.
             */
            'nominal_pokok' =>
                $this->money($nominalPokok),

            'nominal_bunga' =>
                $this->money($nominalBunga),

            /*
             * Denda berjalan berdasarkan tanggal pembayaran.
             */
            'denda_berjalan' =>
                $this->money($dendaBerjalan),

            /*
             * Histori pembayaran valid.
             */
            'sudah_dibayar_denda' =>
                $this->money(
                    $pembayaranSebelumnya['denda']
                ),

            'sudah_dibayar_bunga' =>
                $this->money(
                    $pembayaranSebelumnya['bunga']
                ),

            'sudah_dibayar_pokok' =>
                $this->money(
                    $pembayaranSebelumnya['pokok']
                ),

            /*
             * Sisa masing-masing komponen.
             */
            'sisa_denda' =>
                $this->money($sisaDenda),

            'sisa_bunga' =>
                $this->money($sisaBunga),

            'sisa_pokok' =>
                $this->money($sisaPokok),

            /*
             * Total tagihan yang masih harus dibayar.
             */
            'total_tagihan' =>
                $totalTagihan,

            /*
             * Status lunas.
             */
            'sudah_lunas' =>
                $totalTagihan <= 0.0,
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * Preview
     * ----------------------------------------------------------------------
     *
     * Alias untuk kebutuhan Controller / AJAX.
     */
    public function preview(
        int $piutangId,
        string $tanggalPembayaran
    ): array {
        return $this->calculatePayment(
            $piutangId,
            $tanggalPembayaran
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Allocate Payment
     * ----------------------------------------------------------------------
     *
     * Mengalokasikan pembayaran:
     *
     *      Denda → Bunga → Pokok
     *
     * Pembayaran tidak boleh melebihi total tagihan.
     *
     * @throws RuntimeException
     */
    public function allocatePayment(
        int $piutangId,
        string $tanggalPembayaran,
        float $nominalPembayaran
    ): array {
        $nominalPembayaran =
            $this->money($nominalPembayaran);

        if ($nominalPembayaran <= 0) {
            throw new RuntimeException(
                'Nominal pembayaran harus lebih dari 0.'
            );
        }

        $calculation =
            $this->calculatePayment(
                $piutangId,
                $tanggalPembayaran
            );

        $totalTagihan =
            (float) $calculation['total_tagihan'];

        if ($totalTagihan <= 0) {
            throw new RuntimeException(
                'Piutang sudah lunas.'
            );
        }

        /*
         * Overpayment tidak diperbolehkan.
         */
        if ($nominalPembayaran > $totalTagihan) {
            throw new RuntimeException(
                'Nominal pembayaran tidak boleh melebihi total tagihan.'
            );
        }

        /*
         * --------------------------------------------------------------
         * 1. Denda
         * --------------------------------------------------------------
         */

        $alokasiDenda =
            min(
                $nominalPembayaran,
                (float) $calculation['sisa_denda']
            );

        $alokasiDenda =
            $this->money($alokasiDenda);

        $sisaPembayaran =
            $nominalPembayaran
            - $alokasiDenda;

        /*
         * --------------------------------------------------------------
         * 2. Bunga
         * --------------------------------------------------------------
         */

        $alokasiBunga =
            min(
                $sisaPembayaran,
                (float) $calculation['sisa_bunga']
            );

        $alokasiBunga =
            $this->money($alokasiBunga);

        $sisaPembayaran =
            $sisaPembayaran
            - $alokasiBunga;

        /*
         * --------------------------------------------------------------
         * 3. Pokok
         * --------------------------------------------------------------
         */

        $alokasiPokok =
            min(
                $sisaPembayaran,
                (float) $calculation['sisa_pokok']
            );

        $alokasiPokok =
            $this->money($alokasiPokok);

        /*
         * --------------------------------------------------------------
         * Validasi total alokasi
         * --------------------------------------------------------------
         *
         * Denda + Bunga + Pokok harus sama dengan
         * nominal pembayaran.
         */

        $totalAlokasi =
            $alokasiDenda
            + $alokasiBunga
            + $alokasiPokok;

        $totalAlokasi =
            $this->money($totalAlokasi);

        if (
            abs(
                $totalAlokasi
                - $nominalPembayaran
            ) > 0.01
        ) {
            throw new RuntimeException(
                'Alokasi pembayaran tidak sesuai dengan nominal pembayaran.'
            );
        }

        /*
         * --------------------------------------------------------------
         * Sisa tagihan
         * --------------------------------------------------------------
         */

        $sisaTagihan =
            $totalTagihan
            - $nominalPembayaran;

        if (abs($sisaTagihan) < 0.01) {
            $sisaTagihan = 0.0;
        }

        $sisaTagihan =
            $this->money($sisaTagihan);

        return [
            'total_tagihan' =>
                $totalTagihan,

            'nominal_pembayaran' =>
                $nominalPembayaran,

            'alokasi_denda' =>
                $alokasiDenda,

            'alokasi_bunga' =>
                $alokasiBunga,

            'alokasi_pokok' =>
                $alokasiPokok,

            'sisa_tagihan' =>
                $sisaTagihan,
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * Create Payment
     * ----------------------------------------------------------------------
     *
     * Membuat transaksi pembayaran baru.
     *
     * Pembayaran yang sudah dibuat tidak boleh diedit.
     *
     * @throws Throwable
     */
    public function createPayment(
        int $piutangId,
        string $tanggalPembayaran,
        float $nominalPembayaran,
        int $createdBy,
        ?string $keterangan = null
    ): int {
        /*
         * Validasi user.
         */
        if ($createdBy <= 0) {
            throw new RuntimeException(
                'User pembuat pembayaran tidak valid.'
            );
        }

        /*
         * Normalisasi nominal.
         */
        $nominalPembayaran =
            $this->money($nominalPembayaran);

        /*
         * --------------------------------------------------------------
         * Mulai transaksi database
         * --------------------------------------------------------------
         */

        $this->db->transBegin();

        try {

            /*
             * ----------------------------------------------------------
             * Hitung ulang di dalam transaksi
             * ----------------------------------------------------------
             *
             * Jangan hanya mengandalkan preview dari browser.
             *
             * Data harus dihitung ulang saat benar-benar disimpan.
             */

            $allocation =
                $this->allocatePayment(
                    $piutangId,
                    $tanggalPembayaran,
                    $nominalPembayaran
                );

            /*
             * ----------------------------------------------------------
             * Generate nomor pembayaran
             * ----------------------------------------------------------
             */

            $nomorPembayaran =
                $this->generateUniquePaymentNumber();

            /*
             * ----------------------------------------------------------
             * Data pembayaran
             * ----------------------------------------------------------
             */

            $data = [
                'piutang_id' =>
                    $piutangId,

                'nomor_pembayaran' =>
                    $nomorPembayaran,

                'tanggal_pembayaran' =>
                    $tanggalPembayaran,

                /*
                 * Snapshot tagihan.
                 */
                'total_tagihan' =>
                    $allocation['total_tagihan'],

                /*
                 * Nominal transaksi.
                 */
                'nominal_pembayaran' =>
                    $allocation['nominal_pembayaran'],

                /*
                 * Alokasi:
                 *
                 * Denda → Bunga → Pokok
                 */
                'alokasi_denda' =>
                    $allocation['alokasi_denda'],

                'alokasi_bunga' =>
                    $allocation['alokasi_bunga'],

                'alokasi_pokok' =>
                    $allocation['alokasi_pokok'],

                /*
                 * Snapshot sisa setelah transaksi.
                 */
                'sisa_tagihan' =>
                    $allocation['sisa_tagihan'],

                /*
                 * Status awal.
                 */
                'status' =>
                    PembayaranModel::STATUS_VALID,

                /*
                 * Keterangan.
                 */
                'keterangan' =>
                    $keterangan !== null
                        && trim($keterangan) !== ''
                        ? trim($keterangan)
                        : null,

                /*
                 * Audit.
                 */
                'created_by' =>
                    $createdBy,
            ];

            /*
             * ----------------------------------------------------------
             * Insert
             * ----------------------------------------------------------
             */

            $paymentId =
                $this->pembayaranModel->insert(
                    $data,
                    true
                );

            if ($paymentId === false) {
                throw new RuntimeException(
                    'Pembayaran gagal disimpan.'
                );
            }

            /*
             * ----------------------------------------------------------
             * Pastikan transaction database berhasil
             * ----------------------------------------------------------
             */

            if ($this->db->transStatus() === false) {
                throw new RuntimeException(
                    'Transaksi pembayaran gagal diproses.'
                );
            }

            $this->db->transCommit();

            return (int) $paymentId;

        } catch (Throwable $e) {

            $this->db->transRollback();

            throw $e;
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Cancel Payment
     * ----------------------------------------------------------------------
     *
     * Pembayaran tidak dihapus.
     *
     * Status berubah:
     *
     *      valid
     *        ↓
     *      dibatalkan
     *
     * Record tetap tersimpan sebagai histori.
     *
     * @throws RuntimeException
     */
    public function cancelPayment(
        int $paymentId,
        int $cancelledBy,
        ?string $reason = null
    ): bool {
        if ($cancelledBy <= 0) {
            throw new RuntimeException(
                'User pembatal tidak valid.'
            );
        }

        $payment =
            $this->pembayaranModel->find(
                $paymentId
            );

        if ($payment === null) {
            throw new RuntimeException(
                'Data pembayaran tidak ditemukan.'
            );
        }

        /*
         * Sudah dibatalkan.
         */
        if (
            ($payment['status'] ?? null)
            === PembayaranModel::STATUS_DIBATALKAN
        ) {
            throw new RuntimeException(
                'Pembayaran tersebut sudah dibatalkan.'
            );
        }

        /*
         * Hanya pembayaran VALID yang boleh dibatalkan.
         */
        if (
            ($payment['status'] ?? null)
            !== PembayaranModel::STATUS_VALID
        ) {
            throw new RuntimeException(
                'Status pembayaran tidak dapat dibatalkan.'
            );
        }

        $data = [
            'status' =>
                PembayaranModel::STATUS_DIBATALKAN,

            'cancelled_by' =>
                $cancelledBy,

            'cancelled_at' =>
                date('Y-m-d H:i:s'),
        ];

        /*
         * Alasan pembatalan tetap disimpan
         * di field keterangan.
         */
        if (
            $reason !== null
            && trim($reason) !== ''
        ) {
            $data['alasan_pembatalan'] = trim($reason);
        }

        return (bool) $this->pembayaranModel->update(
            $paymentId,
            $data
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Calculate Penalty
     * ----------------------------------------------------------------------
     *
     * Delegasi perhitungan denda ke PiutangModel.
     *
     * Service TIDAK memiliki formula denda sendiri.
     */
    protected function calculatePenalty(
        array $piutang,
        string $tanggalPembayaran
    ): float {
        return $this->piutangModel->calculatePenalty(
            (float) $piutang['nominal_pokok'],
            (string) $piutang['tanggal_jatuh_tempo'],
            $tanggalPembayaran,
            (float) $piutang['persentase_denda'],
            (int) $piutang['periode_denda_hari'],
            (float) $piutang['maksimal_denda_persen']
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Get Piutang
     * ----------------------------------------------------------------------
     */
    protected function getPiutang(
        int $piutangId
    ): array {
        if ($piutangId <= 0) {
            throw new RuntimeException(
                'Piutang tidak valid.'
            );
        }

        $piutang =
            $this->piutangModel->find(
                $piutangId
            );

        if ($piutang === null) {
            throw new RuntimeException(
                'Data piutang tidak ditemukan.'
            );
        }

        /*
         * Pastikan field snapshot denda tersedia.
         */
        $requiredFields = [
            'nominal_pokok',
            'nominal_bunga',
            'tanggal_piutang',
            'tanggal_jatuh_tempo',
            'persentase_denda',
            'periode_denda_hari',
            'maksimal_denda_persen',
        ];

        foreach ($requiredFields as $field) {
            if (! array_key_exists($field, $piutang)) {
                throw new RuntimeException(
                    'Data piutang tidak lengkap: '
                    . $field
                );
            }
        }

        return $piutang;
    }


    /**
     * ----------------------------------------------------------------------
     * Validate Payment Date
     * ----------------------------------------------------------------------
     *
     * Aturan:
     *
     * tanggal pembayaran >= tanggal piutang
     */
    protected function validatePaymentDate(
        array $piutang,
        string $tanggalPembayaran
    ): void {
        if (
            ! preg_match(
                '/^\d{4}-\d{2}-\d{2}$/',
                $tanggalPembayaran
            )
        ) {
            throw new RuntimeException(
                'Tanggal pembayaran tidak valid.'
            );
        }

        try {
            $tanggalBayar =
                new \DateTimeImmutable(
                    $tanggalPembayaran
                );

            $tanggalPiutang =
                new \DateTimeImmutable(
                    (string) $piutang[
                        'tanggal_piutang'
                    ]
                );

        } catch (Throwable) {
            throw new RuntimeException(
                'Tanggal pembayaran tidak valid.'
            );
        }

        if ($tanggalBayar < $tanggalPiutang) {
            throw new RuntimeException(
                'Tanggal pembayaran tidak boleh lebih awal '
                . 'dari tanggal piutang.'
            );
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Get Previous Allocation
     * ----------------------------------------------------------------------
     *
     * Hanya pembayaran VALID yang diperhitungkan.
     *
     * Pembayaran DIBATALKAN tidak mengurangi saldo.
     */
    protected function getPreviousAllocation(
        int $piutangId
    ): array {
        $result =
            $this->pembayaranModel
                ->select([
                    'COALESCE(SUM(alokasi_denda), 0)'
                    . ' AS total_denda',

                    'COALESCE(SUM(alokasi_bunga), 0)'
                    . ' AS total_bunga',

                    'COALESCE(SUM(alokasi_pokok), 0)'
                    . ' AS total_pokok',
                ])
                ->where(
                    'piutang_id',
                    $piutangId
                )
                ->where(
                    'status',
                    PembayaranModel::STATUS_VALID
                )
                ->first();

        return [
            'denda' =>
                $this->money(
                    (float) (
                        $result['total_denda']
                        ?? 0
                    )
                ),

            'bunga' =>
                $this->money(
                    (float) (
                        $result['total_bunga']
                        ?? 0
                    )
                ),

            'pokok' =>
                $this->money(
                    (float) (
                        $result['total_pokok']
                        ?? 0
                    )
                ),
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * Generate Unique Payment Number
     * ----------------------------------------------------------------------
     *
     * Format:
     *
     * PAY-00001
     * PAY-00002
     * PAY-00003
     *
     * Nomor pembayaran yang pernah digunakan tidak didaur ulang.
     */
    protected function generateUniquePaymentNumber(): string
    {
        $maxAttempt = 10;

        for ($attempt = 0; $attempt < $maxAttempt; $attempt++) {

            $nomor =
                $this->pembayaranModel
                    ->generateNomorPembayaran();

            if (
                ! $this->pembayaranModel
                    ->nomorPembayaranExists($nomor)
            ) {
                return $nomor;
            }
        }

        throw new RuntimeException(
            'Gagal menghasilkan nomor pembayaran unik.'
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Money
     * ----------------------------------------------------------------------
     *
     * Normalisasi nominal ke 2 angka desimal.
     */
    protected function money(
        float|int|string $value
    ): float {
        return round(
            (float) $value,
            2
        );
    }
}