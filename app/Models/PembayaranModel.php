<?php

declare(strict_types=1);

namespace App\Models;

/**
 * --------------------------------------------------------------------------
 * Pembayaran Model
 * --------------------------------------------------------------------------
 *
 * Model transaksi pembayaran pada Sistem Monitoring Piutang.
 *
 * Model ini menangani:
 *
 * - Data pembayaran customer
 * - Relasi pembayaran dengan piutang
 * - Nomor pembayaran
 * - Snapshot total tagihan
 * - Nominal pembayaran
 * - Alokasi pembayaran
 * - Snapshot sisa tagihan
 * - Status pembayaran
 * - Histori pembayaran
 * - Pembatalan pembayaran
 * - Audit trail
 *
 * Business logic pembayaran seperti:
 *
 * - Perhitungan denda
 * - Perhitungan total tagihan
 * - Validasi saldo
 * - Alokasi Denda → Bunga → Pokok
 *
 * TIDAK dilakukan di Model.
 *
 * Logic tersebut menjadi tanggung jawab PaymentService.
 */
class PembayaranModel extends BaseModel
{
    /**
     * ----------------------------------------------------------------------
     * Table Configuration
     * ----------------------------------------------------------------------
     */

    protected $table = 'pembayaran';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    protected $useAutoIncrement = true;

    /**
     * Pembayaran tidak menggunakan soft delete.
     *
     * Koreksi transaksi dilakukan melalui
     * pembatalan pembayaran, bukan penghapusan.
     */
    protected $useSoftDeletes = false;

    protected $useTimestamps = true;

    protected $createdField = 'created_at';

    protected $updatedField = 'updated_at';

    /**
     * ----------------------------------------------------------------------
     * Allowed Fields
     * ----------------------------------------------------------------------
     */

    protected $allowedFields = [
        'piutang_id',

        'nomor_pembayaran',

        'tanggal_pembayaran',

        /*
         * Snapshot tagihan.
         */
        'total_tagihan',

        /*
         * Nominal transaksi pembayaran.
         */
        'nominal_pembayaran',

        /*
         * Alokasi pembayaran.
         *
         * Urutan:
         *
         * Denda → Bunga → Pokok
         */
        'alokasi_denda',
        'alokasi_bunga',
        'alokasi_pokok',

        /*
         * Snapshot sisa tagihan setelah transaksi.
         */
        'sisa_tagihan',

        /*
         * Status transaksi.
         */
        'status',

        'keterangan',

        'alasan_pembatalan',

        /*
         * Audit.
         */
        'created_by',
        'cancelled_by',
        'cancelled_at',
    ];


    /**
     * ----------------------------------------------------------------------
     * Status Constants
     * ----------------------------------------------------------------------
     */

    public const STATUS_VALID = 'valid';

    public const STATUS_DIBATALKAN = 'dibatalkan';


    /**
     * ----------------------------------------------------------------------
     * Validation Rules
     * ----------------------------------------------------------------------
     */

    protected $validationRules = [

        'piutang_id' => [
            'label' => 'Piutang',
            'rules' => 'required|integer|greater_than[0]',
        ],

        'nomor_pembayaran' => [
            'label' => 'Nomor Pembayaran',
            'rules' => 'required|max_length[30]',
        ],

        'tanggal_pembayaran' => [
            'label' => 'Tanggal Pembayaran',
            'rules' => 'required|valid_date[Y-m-d]',
        ],

        'total_tagihan' => [
            'label' => 'Total Tagihan',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],

        'nominal_pembayaran' => [
            'label' => 'Nominal Pembayaran',
            'rules' => 'required|decimal|greater_than[0]',
        ],

        'alokasi_denda' => [
            'label' => 'Alokasi Denda',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],

        'alokasi_bunga' => [
            'label' => 'Alokasi Bunga',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],

        'alokasi_pokok' => [
            'label' => 'Alokasi Pokok',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],

        'sisa_tagihan' => [
            'label' => 'Sisa Tagihan',
            'rules' => 'required|decimal|greater_than_equal_to[0]',
        ],

        'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[valid,dibatalkan]',
        ],

        'keterangan' => [
            'label' => 'Keterangan',
            'rules' => 'permit_empty',
        ],

        'created_by' => [
            'label' => 'Dibuat Oleh',
            'rules' => 'permit_empty|integer|greater_than[0]',
        ],

        'cancelled_by' => [
            'label' => 'Dibatalkan Oleh',
            'rules' => 'permit_empty|integer|greater_than[0]',
        ],

        'cancelled_at' => [
            'label' => 'Waktu Pembatalan',
            'rules' => 'permit_empty|valid_date[Y-m-d H:i:s]',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Validation Messages
     * ----------------------------------------------------------------------
     */

    protected $validationMessages = [

        'piutang_id' => [
            'required' =>
                'Piutang wajib dipilih.',

            'integer' =>
                'ID piutang tidak valid.',

            'greater_than' =>
                'Piutang tidak valid.',
        ],

        'nomor_pembayaran' => [
            'required' =>
                'Nomor pembayaran wajib diisi.',

            'max_length' =>
                'Nomor pembayaran maksimal 30 karakter.',
        ],

        'tanggal_pembayaran' => [
            'required' =>
                'Tanggal pembayaran wajib diisi.',

            'valid_date' =>
                'Format tanggal pembayaran tidak valid.',
        ],

        'total_tagihan' => [
            'required' =>
                'Total tagihan wajib diisi.',

            'decimal' =>
                'Total tagihan harus berupa angka.',

            'greater_than_equal_to' =>
                'Total tagihan tidak boleh negatif.',
        ],

        'nominal_pembayaran' => [
            'required' =>
                'Nominal pembayaran wajib diisi.',

            'decimal' =>
                'Nominal pembayaran harus berupa angka.',

            'greater_than' =>
                'Nominal pembayaran harus lebih dari 0.',
        ],

        'alokasi_denda' => [
            'required' =>
                'Alokasi denda wajib diisi.',

            'decimal' =>
                'Alokasi denda harus berupa angka.',

            'greater_than_equal_to' =>
                'Alokasi denda tidak boleh negatif.',
        ],

        'alokasi_bunga' => [
            'required' =>
                'Alokasi bunga wajib diisi.',

            'decimal' =>
                'Alokasi bunga harus berupa angka.',

            'greater_than_equal_to' =>
                'Alokasi bunga tidak boleh negatif.',
        ],

        'alokasi_pokok' => [
            'required' =>
                'Alokasi pokok wajib diisi.',

            'decimal' =>
                'Alokasi pokok harus berupa angka.',

            'greater_than_equal_to' =>
                'Alokasi pokok tidak boleh negatif.',
        ],

        'sisa_tagihan' => [
            'required' =>
                'Sisa tagihan wajib diisi.',

            'decimal' =>
                'Sisa tagihan harus berupa angka.',

            'greater_than_equal_to' =>
                'Sisa tagihan tidak boleh negatif.',
        ],

        'status' => [
            'required' =>
                'Status pembayaran wajib diisi.',

            'in_list' =>
                'Status pembayaran tidak valid.',
        ],

        'created_by' => [
            'integer' =>
                'User pembuat tidak valid.',

            'greater_than' =>
                'User pembuat tidak valid.',
        ],

        'cancelled_by' => [
            'integer' =>
                'User pembatal tidak valid.',

            'greater_than' =>
                'User pembatal tidak valid.',
        ],

        'cancelled_at' => [
            'valid_date' =>
                'Format waktu pembatalan tidak valid.',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Validation Configuration
     * ----------------------------------------------------------------------
     */

    protected $skipValidation = false;


    /**
     * ----------------------------------------------------------------------
     * Get Payments By Piutang
     * ----------------------------------------------------------------------
     *
     * Mengambil seluruh histori pembayaran untuk satu piutang.
     *
     * Pembayaran dibatalkan tetap ditampilkan karena merupakan bagian
     * dari histori transaksi.
     */

    public function getByPiutang(int $piutangId): array
    {
        return $this
            ->where('piutang_id', $piutangId)
            ->orderBy('tanggal_pembayaran', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }


    /**
     * ----------------------------------------------------------------------
     * Get Valid Payments By Piutang
     * ----------------------------------------------------------------------
     *
     * Hanya mengambil pembayaran yang masih valid.
     *
     * Digunakan untuk menghitung saldo pembayaran aktual.
     */

    public function getValidByPiutang(int $piutangId): array
    {
        return $this
            ->where('piutang_id', $piutangId)
            ->where(
                'status',
                self::STATUS_VALID
            )
            ->orderBy('tanggal_pembayaran', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }


    /**
     * ----------------------------------------------------------------------
     * Get Total Valid Payment
     * ----------------------------------------------------------------------
     *
     * Mengambil total nominal pembayaran yang valid.
     *
     * Pembayaran dibatalkan tidak ikut dihitung.
     */

    public function getTotalValidPayment(
        int $piutangId
    ): float {
        $result = $this
            ->selectSum(
                'nominal_pembayaran',
                'total_pembayaran'
            )
            ->where(
                'piutang_id',
                $piutangId
            )
            ->where(
                'status',
                self::STATUS_VALID
            )
            ->first();

        return (float) (
            $result['total_pembayaran']
            ?? 0
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Get Payment By Number
     * ----------------------------------------------------------------------
     */

    public function getByNumber(
        string $nomorPembayaran
    ): ?array {
        return $this
            ->where(
                'nomor_pembayaran',
                $nomorPembayaran
            )
            ->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Get Payment With Piutang
     * ----------------------------------------------------------------------
     *
     * Mengambil pembayaran beserta informasi piutang dan customer.
     */

    public function getWithPiutang(
        int $id
    ): ?array {
        return $this
            ->select([
                'pembayaran.*',

                'piutang.nomor_piutang',
                'piutang.customer_id',
                'piutang.tanggal_piutang',
                'piutang.tanggal_jatuh_tempo',
                'piutang.nominal_pokok',
                'piutang.persentase_bunga',
                'piutang.nominal_bunga',

                'customer.kode_customer',
                'customer.nama AS nama_customer',
            ])
            ->join(
                'piutang',
                'piutang.id = pembayaran.piutang_id',
                'left'
            )
            ->join(
                'customer',
                'customer.id = piutang.customer_id',
                'left'
            )
            ->where(
                'pembayaran.id',
                $id
            )
            ->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Get All With Piutang
     * ----------------------------------------------------------------------
     *
     * Digunakan untuk halaman index pembayaran.
     */

    public function getAllWithPiutang(): array
    {
        return $this
            ->select([
                'pembayaran.*',

                'piutang.nomor_piutang',
                'piutang.customer_id',
                'piutang.tanggal_piutang',
                'piutang.tanggal_jatuh_tempo',

                'customer.kode_customer',
                'customer.nama AS nama_customer',
            ])
            ->join(
                'piutang',
                'piutang.id = pembayaran.piutang_id',
                'left'
            )
            ->join(
                'customer',
                'customer.id = piutang.customer_id',
                'left'
            )
            ->orderBy(
                'pembayaran.tanggal_pembayaran',
                'DESC'
            )
            ->orderBy(
                'pembayaran.id',
                'DESC'
            )
            ->findAll();
    }


    /**
     * ----------------------------------------------------------------------
     * Count Valid Payments
     * ----------------------------------------------------------------------
     */

    public function countValidByPiutang(
        int $piutangId
    ): int {
        return $this
            ->where(
                'piutang_id',
                $piutangId
            )
            ->where(
                'status',
                self::STATUS_VALID
            )
            ->countAllResults();
    }


    /**
     * ----------------------------------------------------------------------
     * Has Valid Payment
     * ----------------------------------------------------------------------
     */

    public function hasValidPayment(
        int $piutangId
    ): bool {
        return $this->countValidByPiutang(
            $piutangId
        ) > 0;
    }


    /**
     * ----------------------------------------------------------------------
     * Has Any Payment
     * ----------------------------------------------------------------------
     *
     * Termasuk pembayaran yang sudah dibatalkan.
     *
     * Berguna untuk menentukan apakah sebuah piutang
     * pernah memiliki histori pembayaran.
     */

    public function hasAnyPayment(
        int $piutangId
    ): bool {
        return $this
            ->where(
                'piutang_id',
                $piutangId
            )
            ->countAllResults() > 0;
    }


    /**
     * ----------------------------------------------------------------------
     * Generate Payment Number
     * ----------------------------------------------------------------------
     *
     * Format:
     *
     * PAY-00001
     * PAY-00002
     * PAY-00003
     *
     * Nomor tidak didaur ulang setelah transaksi dibatalkan.
     */

    public function generateNomorPembayaran(): string
    {
        $prefix = 'PAY-';

        $last = $this
            ->select('nomor_pembayaran')
            ->like(
                'nomor_pembayaran',
                $prefix,
                'after'
            )
            ->orderBy(
                'id',
                'DESC'
            )
            ->first();

        if ($last === null) {
            return $prefix . '00001';
        }

        $lastNumber = (int) str_replace(
            $prefix,
            '',
            $last['nomor_pembayaran']
        );

        return $prefix .
            str_pad(
                (string) ($lastNumber + 1),
                5,
                '0',
                STR_PAD_LEFT
            );
    }


    /**
     * ----------------------------------------------------------------------
     * Check Payment Number Exists
     * ----------------------------------------------------------------------
     */

    public function nomorPembayaranExists(
        string $nomorPembayaran,
        ?int $exceptId = null
    ): bool {
        $builder = $this
            ->where(
                'nomor_pembayaran',
                $nomorPembayaran
            );

        if ($exceptId !== null) {
            $builder->where(
                'id !=',
                $exceptId
            );
        }

        return $builder->countAllResults() > 0;
    }


    /**
     * ----------------------------------------------------------------------
     * Is Valid
     * ----------------------------------------------------------------------
     */

    public function isValid(array $payment): bool
    {
        return (
            ($payment['status'] ?? null)
            === self::STATUS_VALID
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Is Cancelled
     * ----------------------------------------------------------------------
     */

    public function isCancelled(
        array $payment
    ): bool {
        return (
            ($payment['status'] ?? null)
            === self::STATUS_DIBATALKAN
        );
    }
}