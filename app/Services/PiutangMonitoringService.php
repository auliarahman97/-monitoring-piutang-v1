<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\PembayaranModel;
use App\Models\PiutangModel;
use Throwable;

class PiutangMonitoringService
{
    /**
     * ----------------------------------------------------------------------
     * Models
     * ----------------------------------------------------------------------
     */

    protected PiutangModel $piutangModel;

    protected PembayaranModel $pembayaranModel;


    /**
     * ----------------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------------
     */

    public function __construct(
        ?PiutangModel $piutangModel = null,
        ?PembayaranModel $pembayaranModel = null
    ) {
        $this->piutangModel =
            $piutangModel
            ?? new PiutangModel();


        $this->pembayaranModel =
            $pembayaranModel
            ?? new PembayaranModel();
    }


    /**
     * ----------------------------------------------------------------------
     * BUILD CURRENT REPORT
     * ----------------------------------------------------------------------
     *
     * Menghasilkan kondisi seluruh piutang pada tanggal tertentu.
     *
     * Default:
     * tanggal hari ini.
     *
     * Piutang yang sudah soft-delete tidak ikut karena
     * getAllWithCustomer() menggunakan findAll().
     */
    public function getCurrentReport(
        ?string $tanggalLaporan = null
    ): array {

        $tanggalLaporan =
            $tanggalLaporan
            ?? date('Y-m-d');


        $piutangs =
            $this->piutangModel
                ->getAllWithCustomer();


        $report = [];


        foreach (
            $piutangs as $piutang
        ) {

            $condition =
                $this->calculateCondition(
                    $piutang,
                    $tanggalLaporan
                );


            $report[] =
                array_merge(
                    $piutang,
                    $condition
                );
        }


        return $report;
    }

    /**
     * ----------------------------------------------------------------------
     * CHECK CUSTOMER OUTSTANDING
     * ----------------------------------------------------------------------
     *
     * Mengecek apakah customer masih memiliki minimal satu piutang
     * dengan sisa tagihan lebih dari 0.
     *
     * Rule:
     *
     * - Belum pernah punya piutang
     *      → false
     *
     * - Semua piutang sudah lunas
     *      → false
     *
     * - Minimal satu piutang masih memiliki saldo
     *      → true
     *
     * Perhitungan sisa tagihan tetap menggunakan calculateCondition()
     * agar tidak ada formula piutang kedua.
     */
    public function hasOutstandingByCustomer(
        int $customerId,
        ?string $tanggalLaporan = null
    ): bool {
        $tanggalLaporan =
            $tanggalLaporan
            ?? date('Y-m-d');

        $report =
            $this->getCurrentReport(
                $tanggalLaporan
            );

        foreach ($report as $row) {

            if (
                (int) (
                    $row['customer_id']
                    ?? 0
                ) !== $customerId
            ) {
                continue;
            }

            if (
                (float) (
                    $row['sisa_tagihan']
                    ?? 0
                ) > 0
            ) {
                return true;
            }
        }

        return false;
    }


    /**
     * ----------------------------------------------------------------------
     * CALCULATE CONDITION
     * ----------------------------------------------------------------------
     *
     * Rule:
     *
     * 1. Hanya pembayaran VALID yang dihitung.
     * 2. Jika pernah lunas, tidak ada denda baru.
     * 3. Denda menggunakan PiutangModel::calculatePenalty().
     * 4. Pembayaran dialokasikan:
     *
     *      Denda → Bunga → Pokok
     *
     * 5. Total Tagihan:
     *
     *      Sisa Denda + Sisa Bunga + Sisa Pokok
     *
     * 6. Sisa Tagihan:
     *
     *      Total Tagihan
     */
    public function calculateCondition(
        array $piutang,
        string $tanggalLaporan
    ): array {

        $nominalPokok =
            (float) (
                $piutang[
                    'nominal_pokok'
                ] ?? 0
            );


        $nominalBunga =
            (float) (
                $piutang[
                    'nominal_bunga'
                ] ?? 0
            );


        /*
         * --------------------------------------------------------------
         * PEMBAYARAN VALID
         * --------------------------------------------------------------
         */

        $payments =
            $this->pembayaranModel
                ->getValidByPiutang(
                    (int) (
                        $piutang['id']
                        ?? 0
                    )
                );


        $totalPembayaran = 0.0;

        $totalAlokasiDenda = 0.0;

        $totalAlokasiBunga = 0.0;

        $totalAlokasiPokok = 0.0;


        $pernahLunas = false;


        foreach (
            $payments as $payment
        ) {

            $totalPembayaran +=
                (float) (
                    $payment[
                        'nominal_pembayaran'
                    ] ?? 0
                );


            $totalAlokasiDenda +=
                (float) (
                    $payment[
                        'alokasi_denda'
                    ] ?? 0
                );


            $totalAlokasiBunga +=
                (float) (
                    $payment[
                        'alokasi_bunga'
                    ] ?? 0
                );


            $totalAlokasiPokok +=
                (float) (
                    $payment[
                        'alokasi_pokok'
                    ] ?? 0
                );


            /*
             * Snapshot pembayaran.
             *
             * Jika pernah mencapai 0,
             * piutang pernah lunas.
             */

            if (
                (float) (
                    $payment[
                        'sisa_tagihan'
                    ] ?? 0
                ) <= 0
            ) {

                $pernahLunas = true;
            }
        }


        /*
         * --------------------------------------------------------------
         * SUDAH PERNAH LUNAS
         * --------------------------------------------------------------
         */

        if ($pernahLunas) {

            return [

                'denda_berjalan' =>
                    0.0,

                'total_pembayaran' =>
                    $this->money(
                        $totalPembayaran
                    ),

                'total_alokasi_denda' =>
                    $this->money(
                        $totalAlokasiDenda
                    ),

                'total_alokasi_bunga' =>
                    $this->money(
                        $totalAlokasiBunga
                    ),

                'total_alokasi_pokok' =>
                    $this->money(
                        $totalAlokasiPokok
                    ),

                'sisa_denda' =>
                    0.0,

                'sisa_bunga' =>
                    0.0,

                'sisa_pokok' =>
                    0.0,

                'total_tagihan' =>
                    $this->money(
                        $totalPembayaran
                    ),

                'sisa_tagihan' =>
                    0.0,

                'sudah_lunas' =>
                    true,

                'status_jatuh_tempo' =>
                    'lunas',
            ];
        }


        /*
         * --------------------------------------------------------------
         * DENDA BERJALAN
         * --------------------------------------------------------------
         *
         * Formula TIDAK dibuat ulang di sini.
         *
         * Sumber resmi:
         * PiutangModel::calculatePenalty()
         */

        $dendaBerjalan =
            $this->piutangModel
                ->calculatePenalty(
                    $nominalPokok,

                    (string) (
                        $piutang[
                            'tanggal_jatuh_tempo'
                        ] ?? ''
                    ),

                    $tanggalLaporan,

                    (float) (
                        $piutang[
                            'persentase_denda'
                        ] ?? 0
                    ),

                    (int) (
                        $piutang[
                            'periode_denda_hari'
                        ] ?? 0
                    ),

                    (float) (
                        $piutang[
                            'maksimal_denda_persen'
                        ] ?? 0
                    )
                );


        /*
         * --------------------------------------------------------------
         * SISA KOMPONEN
         * --------------------------------------------------------------
         *
         * Urutan:
         *
         * Denda → Bunga → Pokok
         */

        $sisaDenda =
            max(
                0,
                $dendaBerjalan
                - $totalAlokasiDenda
            );


        $sisaBunga =
            max(
                0,
                $nominalBunga
                - $totalAlokasiBunga
            );


        $sisaPokok =
            max(
                0,
                $nominalPokok
                - $totalAlokasiPokok
            );


        /*
         * --------------------------------------------------------------
         * TOTAL TAGIHAN
         * --------------------------------------------------------------
         */

        $totalTagihan =
            $nominalPokok
            + $nominalBunga
            + $dendaBerjalan;

        $sisaTagihan =
            $sisaDenda
            + $sisaBunga
            + $sisaPokok;
        /*
         * --------------------------------------------------------------
         * STATUS LUNAS
         * --------------------------------------------------------------
         */

        $sudahLunas =
            $sisaTagihan <= 0.01;


        /*
         * --------------------------------------------------------------
         * STATUS JATUH TEMPO
         * --------------------------------------------------------------
         */

        $statusJatuhTempo =
            $this->determineDueStatus(
                (string) (
                    $piutang[
                        'tanggal_jatuh_tempo'
                    ] ?? ''
                ),

                $tanggalLaporan,

                $sudahLunas
            );


        return [

            'denda_berjalan' =>
                $this->money(
                    $dendaBerjalan
                ),

            'total_pembayaran' =>
                $this->money(
                    $totalPembayaran
                ),

            'total_alokasi_denda' =>
                $this->money(
                    $totalAlokasiDenda
                ),

            'total_alokasi_bunga' =>
                $this->money(
                    $totalAlokasiBunga
                ),

            'total_alokasi_pokok' =>
                $this->money(
                    $totalAlokasiPokok
                ),

            'sisa_denda' =>
                $this->money(
                    $sisaDenda
                ),

            'sisa_bunga' =>
                $this->money(
                    $sisaBunga
                ),

            'sisa_pokok' =>
                $this->money(
                    $sisaPokok
                ),

            'total_tagihan' =>
                $this->money(
                    $totalTagihan
                ),

            'sisa_tagihan' =>
                $this->money(
                    $sisaTagihan
                ),

            'sudah_lunas' =>
                $sudahLunas,

            'status_jatuh_tempo' =>
                $statusJatuhTempo,
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * DETERMINE DUE STATUS
     * ----------------------------------------------------------------------
     */

    public function determineDueStatus(
        string $tanggalJatuhTempo,
        string $tanggalLaporan,
        bool $sudahLunas
    ): string {

        if ($sudahLunas) {

            return 'lunas';
        }


        if (
            $tanggalLaporan
            < $tanggalJatuhTempo
        ) {

            return 'belum_jatuh_tempo';
        }


        if (
            $tanggalLaporan
            === $tanggalJatuhTempo
        ) {

            return 'jatuh_tempo';
        }


        return 'menunggak';
    }


    /**
     * ----------------------------------------------------------------------
     * BUILD SUMMARY
     * ----------------------------------------------------------------------
     */

    public function buildSummary(
        array $report
    ): array {

        $totalPiutang = 0.0;

        $totalTagihan = 0.0;

        $totalPembayaran = 0.0;

        $totalSisa = 0.0;

        $totalMenunggak = 0.0;


        $jumlahLunas = 0;

        $jumlahBelumLunas = 0;

        $jumlahBelumJatuhTempo = 0;

        $jumlahJatuhTempo = 0;

        $jumlahMenunggak = 0;


        foreach (
            $report as $row
        ) {

            /*
             * Total Piutang =
             * total pokok yang dipinjam.
             */

            $totalPiutang +=
                (float) (
                    $row[
                        'nominal_pokok'
                    ] ?? 0
                );


            $totalTagihan +=
                (float) (
                    $row[
                        'total_tagihan'
                    ] ?? 0
                );


            $totalPembayaran +=
                (float) (
                    $row[
                        'total_pembayaran'
                    ] ?? 0
                );


            $totalSisa +=
                (float) (
                    $row[
                        'sisa_tagihan'
                    ] ?? 0
                );


            /*
             * Status pembayaran.
             */

            if (
                ($row['sudah_lunas'] ?? false)
            ) {

                $jumlahLunas++;

            } else {

                $jumlahBelumLunas++;
            }


            /*
             * Status jatuh tempo.
             */

            $status =
                $row[
                    'status_jatuh_tempo'
                ] ?? '';


            switch ($status) {

                case 'belum_jatuh_tempo':

                    $jumlahBelumJatuhTempo++;

                    break;


                case 'jatuh_tempo':

                    $jumlahJatuhTempo++;

                    break;


                case 'menunggak':

                    $jumlahMenunggak++;

                    $totalMenunggak +=
                        (float) (
                            $row[
                                'sisa_tagihan'
                            ] ?? 0
                        );

                    break;
            }
        }


        return [

            'jumlah_piutang' =>
                count($report),

            'total_piutang' =>
                $this->money(
                    $totalPiutang
                ),

            'total_tagihan' =>
                $this->money(
                    $totalTagihan
                ),

            'total_pembayaran' =>
                $this->money(
                    $totalPembayaran
                ),

            'sisa_tagihan' =>
                $this->money(
                    $totalSisa
                ),

            'total_menunggak' =>
                $this->money(
                    $totalMenunggak
                ),

            'jumlah_lunas' =>
                $jumlahLunas,

            'jumlah_belum_lunas' =>
                $jumlahBelumLunas,

            'jumlah_belum_jatuh_tempo' =>
                $jumlahBelumJatuhTempo,

            'jumlah_jatuh_tempo' =>
                $jumlahJatuhTempo,

            'jumlah_menunggak' =>
                $jumlahMenunggak,
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * OVERDUE
     * ----------------------------------------------------------------------
     *
     * Mengambil piutang yang sedang menunggak.
     */

    public function getOverdue(
        array $report,
        int $limit = 5
    ): array {

        $overdue = [];


        foreach (
            $report as $row
        ) {

            if (
                ($row[
                    'status_jatuh_tempo'
                ] ?? '') !== 'menunggak'
            ) {

                continue;
            }


            $overdue[] = $row;
        }


        usort(
            $overdue,
            static function (
                array $a,
                array $b
            ): int {

                return strcmp(
                    (string) (
                        $a[
                            'tanggal_jatuh_tempo'
                        ] ?? ''
                    ),

                    (string) (
                        $b[
                            'tanggal_jatuh_tempo'
                        ] ?? ''
                    )
                );
            }
        );


        return array_slice(
            $overdue,
            0,
            $limit
        );
    }


    /**
     * ----------------------------------------------------------------------
     * LATEST PIUTANG
     * ----------------------------------------------------------------------
     */

    public function getLatestPiutang(
        int $limit = 5
    ): array {

        $piutangs =
            $this->getCurrentReport();


        return array_slice(
            $piutangs,
            0,
            $limit
        );
    }


    /**
     * ----------------------------------------------------------------------
     * LATEST PEMBAYARAN
     * ----------------------------------------------------------------------
     */

    public function getLatestPembayaran(
        int $limit = 5
    ): array {

        $pembayaran =
            $this->pembayaranModel
                ->getAllWithPiutang();


        return array_slice(
            $pembayaran,
            0,
            $limit
        );
    }


    /**
     * ----------------------------------------------------------------------
     * MONEY
     * ----------------------------------------------------------------------
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