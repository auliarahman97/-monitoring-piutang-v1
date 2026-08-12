<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\PembayaranModel;
use App\Models\PiutangModel;
use App\Services\PaymentService;
use App\Services\PiutangMonitoringService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Laporan extends BaseController
{
    /**
     * ----------------------------------------------------------------------
     * Models
     * ----------------------------------------------------------------------
     */

    protected PiutangModel $piutangModel;

    protected PembayaranModel $pembayaranModel;

    protected CustomerModel $customerModel;

    protected PaymentService $paymentService;

    protected PiutangMonitoringService $piutangMonitoringService;


    /**
     * ----------------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------------
     */

    public function __construct()
    {
        $this->piutangModel =
            new PiutangModel();

        $this->pembayaranModel =
            new PembayaranModel();

        $this->customerModel =
            new CustomerModel();

        $this->paymentService =
            new PaymentService(
                $this->piutangModel,
                $this->pembayaranModel
            );

        $this->piutangMonitoringService =
            new PiutangMonitoringService(
                $this->piutangModel,
                $this->pembayaranModel
            );
    }


    /**
     * ----------------------------------------------------------------------
     * LAPORAN PIUTANG
     * ----------------------------------------------------------------------
     *
     * GET /laporan/piutang
     *
     * Untuk tahap awal/testing, method ini mengembalikan JSON.
     *
     * Setelah seluruh perhitungan tervalidasi, return JSON akan diganti
     * dengan view laporan/piutang/index.
     */
    public function piutang(): string
    {
        try {

            /*
             * --------------------------------------------------------------
             * 1. Filter
             * --------------------------------------------------------------
             */

            $filter =
                $this->buildPiutangFilter();


            /*
             * --------------------------------------------------------------
             * 2. Report
             * --------------------------------------------------------------
             */

            $report =
                $this->buildPiutangReport(
                    $filter
                );


            /*
             * --------------------------------------------------------------
             * 3. Summary
             * --------------------------------------------------------------
             */

            $summary =
                $this->buildPiutangSummary(
                    $report
                );


            /*
             * --------------------------------------------------------------
             * 4. JSON
             * --------------------------------------------------------------
             *
             * Sementara digunakan untuk testing backend.
             */

            return view(
                'laporan/piutang/index',
                [
                    'title' =>
                        'Laporan Piutang',

                    'filter' =>
                        $filter,

                    'summary' =>
                        $summary,

                    'report' =>
                        $report,

                    'customers' =>
                        $this->getCustomerList(),
                ]
            );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Laporan Piutang Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,

                    'message' =>
                        'Laporan piutang gagal diproses.',

                    'error' =>
                        ENVIRONMENT === 'development'
                            ? $e->getMessage()
                            : null,
                ]);
        }
    }


    /**
     * ----------------------------------------------------------------------
     * BUILD PIUTANG FILTER
     * ----------------------------------------------------------------------
     *
     * Filter menggunakan GET.
     *
     * Parameter:
     *
     * - tanggal_dari
     * - tanggal_sampai
     * - customer_id
     * - status
     * - jatuh_tempo
     *
     * Status:
     *
     * - semua
     * - lunas
     * - belum_lunas
     *
     * Jatuh tempo:
     *
     * - semua
     * - belum_jatuh_tempo
     * - jatuh_tempo
     * - menunggak
     */
    protected function buildPiutangFilter(): array
    {
        /*
         * --------------------------------------------------------------
         * Tanggal dari
         * --------------------------------------------------------------
         */

        $tanggalDari =
            trim(
                (string) $this->request
                    ->getGet('tanggal_dari')
            );


        /*
         * --------------------------------------------------------------
         * Tanggal sampai
         * --------------------------------------------------------------
         */

        $tanggalSampai =
            trim(
                (string) $this->request
                    ->getGet('tanggal_sampai')
            );


        /*
         * --------------------------------------------------------------
         * Validasi tanggal
         * --------------------------------------------------------------
         */

        if (
            $tanggalDari !== ''
            && ! $this->isValidDate(
                $tanggalDari
            )
        ) {
            $tanggalDari = '';
        }


        if (
            $tanggalSampai !== ''
            && ! $this->isValidDate(
                $tanggalSampai
            )
        ) {
            $tanggalSampai = '';
        }


        /*
         * Jika terbalik, tukarkan.
         */

        if (
            $tanggalDari !== ''
            && $tanggalSampai !== ''
            && $tanggalDari > $tanggalSampai
        ) {
            [
                $tanggalDari,
                $tanggalSampai,
            ] = [
                $tanggalSampai,
                $tanggalDari,
            ];
        }


        /*
         * --------------------------------------------------------------
         * Customer
         * --------------------------------------------------------------
         */

        $customerIdRaw =
            $this->request
                ->getGet('customer_id');


        $customerId = null;


        if (
            $customerIdRaw !== null
            && $customerIdRaw !== ''
            && ctype_digit(
                (string) $customerIdRaw
            )
        ) {

            $customerId =
                (int) $customerIdRaw;


            if ($customerId <= 0) {
                $customerId = null;
            }
        }


        /*
         * --------------------------------------------------------------
         * Status Piutang
         * --------------------------------------------------------------
         */

        $status =
            strtolower(
                trim(
                    (string) $this->request
                        ->getGet('status')
                )
            );


        if (
            ! in_array(
                $status,
                [
                    'semua',
                    'lunas',
                    'belum_lunas',
                ],
                true
            )
        ) {
            $status = 'semua';
        }


        /*
         * --------------------------------------------------------------
         * Status Jatuh Tempo
         * --------------------------------------------------------------
         */

        $jatuhTempo =
            strtolower(
                trim(
                    (string) $this->request
                        ->getGet('jatuh_tempo')
                )
            );


        if (
            ! in_array(
                $jatuhTempo,
                [
                    'semua',
                    'belum_jatuh_tempo',
                    'jatuh_tempo',
                    'menunggak',
                ],
                true
            )
        ) {
            $jatuhTempo = 'semua';
        }


        /*
         * --------------------------------------------------------------
         * Tanggal laporan
         * --------------------------------------------------------------
         *
         * Denda berjalan dihitung sampai tanggal ini.
         */

        $tanggalLaporan =
            date('Y-m-d');


        return [
            'tanggal_dari' =>
                $tanggalDari,

            'tanggal_sampai' =>
                $tanggalSampai,

            'tanggal_laporan' =>
                $tanggalLaporan,

            'customer_id' =>
                $customerId,

            'status' =>
                $status,

            'jatuh_tempo' =>
                $jatuhTempo,
        ];
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD PIUTANG REPORT
     * ----------------------------------------------------------------------
     */
    protected function buildPiutangReport(
        array $filter
    ): array {

        /*
        * --------------------------------------------------------------
        * Ambil seluruh piutang beserta customer.
        * --------------------------------------------------------------
        *
        * Tidak memfilter customer.status.
        *
        * Customer nonaktif / histori tetap dapat muncul
        * dalam laporan.
        */

        $piutangs =
            $this->piutangModel
                ->getAllWithCustomer();


        $report = [];


        foreach (
            $piutangs as $piutang
        ) {

            /*
            * ----------------------------------------------------------
            * FILTER TANGGAL PIUTANG
            * ----------------------------------------------------------
            */

            if (
                $filter['tanggal_dari'] !== ''
                && $piutang['tanggal_piutang']
                    < $filter['tanggal_dari']
            ) {
                continue;
            }


            if (
                $filter['tanggal_sampai'] !== ''
                && $piutang['tanggal_piutang']
                    > $filter['tanggal_sampai']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * FILTER CUSTOMER
            * ----------------------------------------------------------
            */

            if (
                $filter['customer_id'] !== null
                && (int) $piutang['customer_id']
                    !== (int) $filter['customer_id']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * KONDISI PIUTANG
            * ----------------------------------------------------------
            *
            * Seluruh business rule:
            *
            * - pembayaran valid
            * - alokasi pembayaran
            * - denda
            * - sisa komponen
            * - total tagihan
            * - sisa tagihan
            * - status lunas
            * - status jatuh tempo
            *
            * ditangani oleh PiutangMonitoringService.
            */

            $condition =
                $this->piutangMonitoringService
                    ->calculateCondition(
                        $piutang,
                        $filter['tanggal_laporan']
                    );


            /*
            * ----------------------------------------------------------
            * FILTER STATUS
            * ----------------------------------------------------------
            */

            if (
                $filter['status'] === 'lunas'
                && ! $condition['sudah_lunas']
            ) {
                continue;
            }


            if (
                $filter['status'] === 'belum_lunas'
                && $condition['sudah_lunas']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * FILTER JATUH TEMPO
            * ----------------------------------------------------------
            */

            if (
                $filter['jatuh_tempo'] !== 'semua'
                && $condition['status_jatuh_tempo']
                    !== $filter['jatuh_tempo']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * GABUNGKAN DATA
            * ----------------------------------------------------------
            *
            * Tidak menghitung ulang nilai finansial di controller.
            */

            $report[] =
                array_merge(
                    $piutang,
                    $condition,
                    [
                        'nominal_pokok' =>
                            $this->money(
                                (float) (
                                    $piutang[
                                        'nominal_pokok'
                                    ] ?? 0
                                )
                            ),

                        'nominal_bunga' =>
                            $this->money(
                                (float) (
                                    $piutang[
                                        'nominal_bunga'
                                    ] ?? 0
                                )
                            ),

                        'total_piutang' =>
                            $this->money(
                                (float) (
                                    $piutang[
                                        'nominal_pokok'
                                    ] ?? 0
                                )
                            ),
                    ]
                );
        }


        return $report;
    }


    /**
     * ----------------------------------------------------------------------
     * BUILD SUMMARY
     * ----------------------------------------------------------------------
     */
    protected function buildPiutangSummary(
        array $report
    ): array {

        $totalTagihan = 0.0;

        $totalPembayaran = 0.0;

        $totalSisa = 0.0;

        $totalMenunggak = 0.0;


        foreach (
            $report as $row
        ) {

            $totalTagihan +=
                (float) (
                    $row['total_tagihan']
                    ?? 0
                );


            $totalPembayaran +=
                (float) (
                    $row['total_pembayaran']
                    ?? 0
                );


            $totalSisa +=
                (float) (
                    $row['sisa_tagihan']
                    ?? 0
                );


            if (
                ($row['status_jatuh_tempo'] ?? '')
                === 'menunggak'
            ) {

                $totalMenunggak +=
                    (float) (
                        $row['sisa_tagihan']
                        ?? 0
                    );
            }
        }


        return [

            'jumlah_piutang' =>
                count($report),

            'total_tagihan' =>
                $this->money(
                    $totalTagihan
                ),

            'total_pembayaran' =>
                $this->money(
                    $totalPembayaran
                ),

            'total_sisa' =>
                $this->money(
                    $totalSisa
                ),

            'total_menunggak' =>
                $this->money(
                    $totalMenunggak
                ),
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * CUSTOMER LIST
     * ----------------------------------------------------------------------
     *
     * Untuk filter laporan, customer nonaktif tetap boleh tersedia
     * apabila memiliki histori transaksi.
     */
    protected function getCustomerList(): array
    {
        return $this->customerModel
            ->orderBy(
                'nama',
                'ASC'
            )
            ->findAll();
    }


    /**
     * ----------------------------------------------------------------------
     * DATE VALIDATION
     * ----------------------------------------------------------------------
     */
    protected function isValidDate(
        string $date
    ): bool {

        $dateObject =
            \DateTimeImmutable::createFromFormat(
                'Y-m-d',
                $date
            );


        return $dateObject !== false
            && $dateObject->format('Y-m-d')
                === $date;
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


    /**
     * ----------------------------------------------------------------------
     * EXPORT PDF
     * ----------------------------------------------------------------------
     *
     * Akan diaktifkan setelah View HTML laporan selesai.
     */
    public function piutangPdf(): ResponseInterface
    {
        try {

            // ==============================================================
            // FILTER
            // ==============================================================

            $filter =
                $this->buildPiutangFilter();


            // ==============================================================
            // REPORT
            // ==============================================================

            $report =
                $this->buildPiutangReport(
                    $filter
                );


            // ==============================================================
            // SUMMARY
            // ==============================================================

            $summary =
                $this->buildPiutangSummary(
                    $report
                );


            // ==============================================================
            // CUSTOMER
            // ==============================================================
            //
            // Dibutuhkan oleh header PDF untuk menampilkan nama customer
            // berdasarkan customer_id.
            //

            $customers =
                $this->getCustomerList();


            // ==============================================================
            // RENDER HTML PDF
            // ==============================================================

            $html =
                view(
                    'laporan/piutang/pdf/index',
                    [
                        'title' =>
                            'Laporan Piutang',

                        'filter' =>
                            $filter,

                        'summary' =>
                            $summary,

                        'report' =>
                            $report,

                        'customers' =>
                            $customers,
                    ]
                );


            // ==============================================================
            // DOMPDF OPTIONS
            // ==============================================================

            $options =
                new \Dompdf\Options();

            $options->set(
                'isHtml5ParserEnabled',
                true
            );

            $options->set(
                'isRemoteEnabled',
                true
            );


            // ==============================================================
            // DOMPDF
            // ==============================================================

            $dompdf =
                new \Dompdf\Dompdf(
                    $options
                );


            $dompdf->loadHtml(
                $html,
                'UTF-8'
            );


            // ==============================================================
            // PAPER
            // ==============================================================

            $dompdf->setPaper(
                'A4',
                'landscape'
            );


            // ==============================================================
            // RENDER
            // ==============================================================

            $dompdf->render();


            // ==============================================================
            // FILE NAME
            // ==============================================================

            $filename =
                'laporan-piutang-'
                . date('Ymd-His')
                . '.pdf';


            // ==============================================================
            // OUTPUT
            // ==============================================================

            return $this->response
                ->setContentType(
                    'application/pdf'
                )
                ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="' . $filename . '"'
                )
                ->setBody(
                    $dompdf->output()
                );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Export PDF Laporan Piutang Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    ENVIRONMENT === 'development'
                        ? $e->getMessage()
                        : 'Export PDF gagal diproses.'
                );
        }
    }

    /**
     * ----------------------------------------------------------------------
     * LAPORAN PEMBAYARAN
     * ----------------------------------------------------------------------
     *
     * GET /laporan/pembayaran
     *
     * Menampilkan histori transaksi pembayaran.
     */
    public function pembayaran(): string
    {
        try {

            /*
            * --------------------------------------------------------------
            * 1. FILTER
            * --------------------------------------------------------------
            */

            $filter =
                $this->buildPembayaranFilter();


            /*
            * --------------------------------------------------------------
            * 2. REPORT
            * --------------------------------------------------------------
            */

            $report =
                $this->buildPembayaranReport(
                    $filter
                );


            /*
            * --------------------------------------------------------------
            * 3. SUMMARY
            * --------------------------------------------------------------
            */

            $summary =
                $this->buildPembayaranSummary(
                    $report
                );


            /*
            * --------------------------------------------------------------
            * 4. VIEW
            * --------------------------------------------------------------
            */

            return view(
                'laporan/pembayaran/index',
                [
                    'title' =>
                        'Laporan Pembayaran',

                    'filter' =>
                        $filter,

                    'summary' =>
                        $summary,

                    'report' =>
                        $report,

                    'customers' =>
                        $this->getCustomerList(),
                ]
            );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Laporan Pembayaran Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,

                    'message' =>
                        'Laporan pembayaran gagal diproses.',

                    'error' =>
                        ENVIRONMENT === 'development'
                            ? $e->getMessage()
                            : null,
                ]);
        }
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD PEMBAYARAN FILTER
     * ----------------------------------------------------------------------
     *
     * Filter menggunakan GET.
     *
     * Parameter:
     *
     * - tanggal_dari
     * - tanggal_sampai
     * - customer_id
     * - status
     *
     * Status:
     *
     * - semua
     * - valid
     * - dibatalkan
     */
    protected function buildPembayaranFilter(): array
    {
        /*
        * --------------------------------------------------------------
        * Tanggal dari
        * --------------------------------------------------------------
        */

        $tanggalDari =
            trim(
                (string) $this->request
                    ->getGet('tanggal_dari')
            );


        /*
        * --------------------------------------------------------------
        * Tanggal sampai
        * --------------------------------------------------------------
        */

        $tanggalSampai =
            trim(
                (string) $this->request
                    ->getGet('tanggal_sampai')
            );


        /*
        * --------------------------------------------------------------
        * Validasi tanggal
        * --------------------------------------------------------------
        */

        if (
            $tanggalDari !== ''
            && ! $this->isValidDate(
                $tanggalDari
            )
        ) {
            $tanggalDari = '';
        }


        if (
            $tanggalSampai !== ''
            && ! $this->isValidDate(
                $tanggalSampai
            )
        ) {
            $tanggalSampai = '';
        }


        /*
        * --------------------------------------------------------------
        * Jika tanggal terbalik, tukarkan.
        * --------------------------------------------------------------
        */

        if (
            $tanggalDari !== ''
            && $tanggalSampai !== ''
            && $tanggalDari > $tanggalSampai
        ) {
            [
                $tanggalDari,
                $tanggalSampai,
            ] = [
                $tanggalSampai,
                $tanggalDari,
            ];
        }


        /*
        * --------------------------------------------------------------
        * Customer
        * --------------------------------------------------------------
        */

        $customerIdRaw =
            $this->request
                ->getGet('customer_id');


        $customerId = null;


        if (
            $customerIdRaw !== null
            && $customerIdRaw !== ''
            && ctype_digit(
                (string) $customerIdRaw
            )
        ) {

            $customerId =
                (int) $customerIdRaw;


            if ($customerId <= 0) {
                $customerId = null;
            }
        }


        /*
        * --------------------------------------------------------------
        * Status pembayaran
        * --------------------------------------------------------------
        */

        $status =
            strtolower(
                trim(
                    (string) $this->request
                        ->getGet('status')
                )
            );


        if (
            ! in_array(
                $status,
                [
                    'semua',
                    PembayaranModel::STATUS_VALID,
                    PembayaranModel::STATUS_DIBATALKAN,
                ],
                true
            )
        ) {
            $status = 'semua';
        }


        return [
            'tanggal_dari' =>
                $tanggalDari,

            'tanggal_sampai' =>
                $tanggalSampai,

            'customer_id' =>
                $customerId,

            'status' =>
                $status,
        ];
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD PEMBAYARAN REPORT
     * ----------------------------------------------------------------------
     */
    protected function buildPembayaranReport(
        array $filter
    ): array {

        /*
        * --------------------------------------------------------------
        * Ambil seluruh pembayaran beserta piutang dan customer.
        * --------------------------------------------------------------
        *
        * Pembayaran yang dibatalkan tetap diambil karena merupakan
        * bagian dari histori transaksi.
        */

        $pembayarans =
            $this->pembayaranModel
                ->getAllWithPiutang();


        $report = [];


        foreach (
            $pembayarans as $pembayaran
        ) {

            /*
            * ----------------------------------------------------------
            * Filter tanggal pembayaran
            * ----------------------------------------------------------
            */

            if (
                $filter['tanggal_dari'] !== ''
                && $pembayaran['tanggal_pembayaran']
                    < $filter['tanggal_dari']
            ) {
                continue;
            }


            if (
                $filter['tanggal_sampai'] !== ''
                && $pembayaran['tanggal_pembayaran']
                    > $filter['tanggal_sampai']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * Filter customer
            * ----------------------------------------------------------
            */

            if (
                $filter['customer_id'] !== null
                && (int) $pembayaran['customer_id']
                    !== (int) $filter['customer_id']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * Filter status pembayaran
            * ----------------------------------------------------------
            */

            if (
                $filter['status'] !== 'semua'
                && (
                    $pembayaran['status']
                    ?? null
                ) !== $filter['status']
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * Normalisasi nilai numerik
            * ----------------------------------------------------------
            *
            * Tidak menghitung ulang snapshot pembayaran.
            */

            $report[] = array_merge(
                $pembayaran,
                [
                    'total_tagihan' =>
                        $this->money(
                            $pembayaran[
                                'total_tagihan'
                            ] ?? 0
                        ),

                    'nominal_pembayaran' =>
                        $this->money(
                            $pembayaran[
                                'nominal_pembayaran'
                            ] ?? 0
                        ),

                    'alokasi_denda' =>
                        $this->money(
                            $pembayaran[
                                'alokasi_denda'
                            ] ?? 0
                        ),

                    'alokasi_bunga' =>
                        $this->money(
                            $pembayaran[
                                'alokasi_bunga'
                            ] ?? 0
                        ),

                    'alokasi_pokok' =>
                        $this->money(
                            $pembayaran[
                                'alokasi_pokok'
                            ] ?? 0
                        ),

                    'sisa_tagihan' =>
                        $this->money(
                            $pembayaran[
                                'sisa_tagihan'
                            ] ?? 0
                        ),
                ]
            );
        }


        return $report;
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD PEMBAYARAN SUMMARY
     * ----------------------------------------------------------------------
     */
    protected function buildPembayaranSummary(
        array $report
    ): array {

        $jumlahTransaksi = count(
            $report
        );


        $jumlahValid = 0;

        $jumlahDibatalkan = 0;


        $totalPembayaranValid = 0.0;

        $totalPembayaranDibatalkan = 0.0;


        $totalDendaValid = 0.0;

        $totalBungaValid = 0.0;

        $totalPokokValid = 0.0;


        foreach (
            $report as $row
        ) {

            $status =
                $row['status']
                ?? null;


            $nominal =
                (float) (
                    $row['nominal_pembayaran']
                    ?? 0
                );


            if (
                $status
                === PembayaranModel::STATUS_VALID
            ) {

                $jumlahValid++;


                $totalPembayaranValid +=
                    $nominal;


                $totalDendaValid +=
                    (float) (
                        $row['alokasi_denda']
                        ?? 0
                    );


                $totalBungaValid +=
                    (float) (
                        $row['alokasi_bunga']
                        ?? 0
                    );


                $totalPokokValid +=
                    (float) (
                        $row['alokasi_pokok']
                        ?? 0
                    );


                continue;
            }


            if (
                $status
                === PembayaranModel::STATUS_DIBATALKAN
            ) {

                $jumlahDibatalkan++;


                $totalPembayaranDibatalkan +=
                    $nominal;
            }
        }


        return [

            'jumlah_transaksi' =>
                $jumlahTransaksi,

            'jumlah_valid' =>
                $jumlahValid,

            'jumlah_dibatalkan' =>
                $jumlahDibatalkan,

            'total_pembayaran_valid' =>
                $this->money(
                    $totalPembayaranValid
                ),

            'total_pembayaran_dibatalkan' =>
                $this->money(
                    $totalPembayaranDibatalkan
                ),

            'total_alokasi_denda' =>
                $this->money(
                    $totalDendaValid
                ),

            'total_alokasi_bunga' =>
                $this->money(
                    $totalBungaValid
                ),

            'total_alokasi_pokok' =>
                $this->money(
                    $totalPokokValid
                ),
        ];
    }

    /**
     * ----------------------------------------------------------------------
     * EXPORT PDF LAPORAN PEMBAYARAN
     * ----------------------------------------------------------------------
     *
     * GET /laporan/pembayaran/pdf
     *
     * Filter:
     *
     * - tanggal_dari
     * - tanggal_sampai
     * - customer_id
     * - status
     */
    public function pembayaranPdf()
    {
        try {

            /*
            * --------------------------------------------------------------
            * FILTER
            * --------------------------------------------------------------
            */

            $filter =
                $this->buildPembayaranFilter();


            /*
            * --------------------------------------------------------------
            * REPORT
            * --------------------------------------------------------------
            */

            $report =
                $this->buildPembayaranReport(
                    $filter
                );


            /*
            * --------------------------------------------------------------
            * SUMMARY
            * --------------------------------------------------------------
            */

            $summary =
                $this->buildPembayaranSummary(
                    $report
                );


            /*
            * --------------------------------------------------------------
            * CUSTOMER LIST
            * --------------------------------------------------------------
            */

            $customers =
                $this->getCustomerList();


            /*
            * --------------------------------------------------------------
            * PDF VIEW
            * --------------------------------------------------------------
            */

            $html =
                view(
                    'laporan/pembayaran/pdf/index',
                    [
                        'title' =>
                            'Laporan Pembayaran',

                        'filter' =>
                            $filter,

                        'report' =>
                            $report,

                        'summary' =>
                            $summary,

                        'customers' =>
                            $customers,
                    ]
                );


            /*
            * --------------------------------------------------------------
            * DOMPDF
            * --------------------------------------------------------------
            */

            $options =
                new \Dompdf\Options();

            $options->set(
                'isRemoteEnabled',
                true
            );

            $options->set(
                'isHtml5ParserEnabled',
                true
            );


            $dompdf =
                new \Dompdf\Dompdf(
                    $options
                );


            $dompdf->loadHtml(
                $html
            );


            /*
            * --------------------------------------------------------------
            * LANDSCAPE A4
            * --------------------------------------------------------------
            */

            $dompdf->setPaper(
                'A4',
                'landscape'
            );


            $dompdf->render();


            /*
            * --------------------------------------------------------------
            * FILE NAME
            * --------------------------------------------------------------
            */

            $filename =
                'laporan-pembayaran-'
                . date('Ymd-His')
                . '.pdf';


            /*
            * --------------------------------------------------------------
            * DOWNLOAD
            * --------------------------------------------------------------
            *
            * attachment = langsung download.
            *
            * Tidak membuka tab PDF baru.
            */

            return $this->response
                ->setContentType(
                    'application/pdf'
                )
                ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'
                    . $filename
                    . '"'
                )
                ->setBody(
                    $dompdf->output()
                );

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Export PDF Laporan Pembayaran Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    ENVIRONMENT === 'development'
                        ? $e->getMessage()
                        : 'Export PDF laporan pembayaran gagal.'
                );
        }
    }

    /**
     * ----------------------------------------------------------------------
     * GET CUSTOMER ID
     * ----------------------------------------------------------------------
     */
    protected function getCustomerId(): ?int
    {
        $customerIdRaw =
            $this->request
                ->getGet('customer_id');


        if (
            $customerIdRaw === null
            || $customerIdRaw === ''
            || ! ctype_digit(
                (string) $customerIdRaw
            )
        ) {
            return null;
        }


        $customerId =
            (int) $customerIdRaw;


        return $customerId > 0
            ? $customerId
            : null;
    }

    /**
     * ----------------------------------------------------------------------
     * LAPORAN CUSTOMER
     * ----------------------------------------------------------------------
     *
     * GET /laporan/customer
     *
     * Customer Statement untuk satu customer.
     *
     * Isi:
     *
     * - Informasi customer
     * - Ringkasan piutang
     * - Riwayat piutang
     * - Riwayat pembayaran
     */
    public function customer(): string|ResponseInterface
    {
        try {

            /*
            * --------------------------------------------------------------
            * CUSTOMER ID
            * --------------------------------------------------------------
            */

            $customerIdRaw =
                $this->request
                    ->getGet('customer_id');


            $customerId = null;


            if (
                $customerIdRaw !== null
                && $customerIdRaw !== ''
                && ctype_digit(
                    (string) $customerIdRaw
                )
            ) {

                $customerId =
                    (int) $customerIdRaw;


                if ($customerId <= 0) {
                    $customerId = null;
                }
            }


            /*
            * --------------------------------------------------------------
            * DAFTAR CUSTOMER
            * --------------------------------------------------------------
            *
            * Customer nonaktif / soft-delete tetap tersedia karena
            * laporan merupakan histori.
            */

            $customers =
                $this->getCustomerList();


            /*
            * --------------------------------------------------------------
            * DEFAULT
            * --------------------------------------------------------------
            */

            $customer = null;


            $piutangReport = [];


            $pembayaranReport = [];


            $summary =
                $this->buildCustomerSummary(
                    [],
                    []
                );


            /*
            * --------------------------------------------------------------
            * JIKA CUSTOMER DIPILIH
            * --------------------------------------------------------------
            */

            if ($customerId !== null) {

                /*
                * Customer soft-delete tetap dapat ditemukan.
                */

                $customer =
                    $this->customerModel
                        ->withDeleted()
                        ->find(
                            $customerId
                        );


                /*
                * Customer tidak ditemukan.
                */

                if ($customer === null) {

                    return view(
                        'laporan/customer/index',
                        [
                            'title' =>
                                'Laporan Customer',

                            'customers' =>
                                $customers,

                            'customer' =>
                                null,

                            'customerId' =>
                                $customerId,

                            'piutangReport' =>
                                [],

                            'pembayaranReport' =>
                                [],

                            'summary' =>
                                $summary,

                            'error' =>
                                'Customer tidak ditemukan.',
                        ]
                    );
                }


                /*
                * ----------------------------------------------------------
                * REPORT
                * ----------------------------------------------------------
                */

                $customerReport =
                    $this->buildCustomerReport(
                        $customerId
                    );


                $piutangReport =
                    $customerReport[
                        'piutang'
                    ];


                $pembayaranReport =
                    $customerReport[
                        'pembayaran'
                    ];


                /*
                * ----------------------------------------------------------
                * SUMMARY
                * ----------------------------------------------------------
                */

                $summary =
                    $this->buildCustomerSummary(
                        $piutangReport,
                        $pembayaranReport
                    );
            }


            /*
            * --------------------------------------------------------------
            * VIEW
            * --------------------------------------------------------------
            */

            return view(
                'laporan/customer/index',
                [
                    'title' =>
                        'Laporan Customer',

                    'customers' =>
                        $customers,

                    'customer' =>
                        $customer,

                    'customerId' =>
                        $customerId,

                    'piutangReport' =>
                        $piutangReport,

                    'pembayaranReport' =>
                        $pembayaranReport,

                    'summary' =>
                        $summary,

                    'error' =>
                        null,
                ]
            );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Laporan Customer Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,

                    'message' =>
                        'Laporan customer gagal diproses.',

                    'error' =>
                        ENVIRONMENT === 'development'
                            ? $e->getMessage()
                            : null,
                ]);
        }
    }

    /**
     * ----------------------------------------------------------------------
     * PRINT LAPORAN CUSTOMER
     * ----------------------------------------------------------------------
     *
     * GET /laporan/customer/print
     *
     * Menampilkan versi siap cetak laporan customer.
     */
    public function customerPrint(): string|ResponseInterface
    {
        try {

            $customerId =
                $this->getCustomerId();


            if ($customerId === null) {

                return redirect()
                    ->to(
                        site_url(
                            'laporan/customer'
                        )
                    )
                    ->with(
                        'error',
                        'Customer belum dipilih.'
                    );
            }


            /*
            * --------------------------------------------------------------
            * CUSTOMER
            * --------------------------------------------------------------
            */

            $customer =
                $this->customerModel
                    ->withDeleted()
                    ->find(
                        $customerId
                    );


            if ($customer === null) {

                return redirect()
                    ->to(
                        site_url(
                            'laporan/customer'
                        )
                    )
                    ->with(
                        'error',
                        'Customer tidak ditemukan.'
                    );
            }


            /*
            * --------------------------------------------------------------
            * REPORT
            * --------------------------------------------------------------
            */

            $customerReport =
                $this->buildCustomerReport(
                    $customerId
                );


            $piutangReport =
                $customerReport['piutang'];


            $pembayaranReport =
                $customerReport['pembayaran'];


            /*
            * --------------------------------------------------------------
            * SUMMARY
            * --------------------------------------------------------------
            */

            $summary =
                $this->buildCustomerSummary(
                    $piutangReport,
                    $pembayaranReport
                );


            /*
            * --------------------------------------------------------------
            * VIEW
            * --------------------------------------------------------------
            */

            return view(
                'laporan/customer/print/index',
                [
                    'title' =>
                        'Laporan Customer',

                    'customer' =>
                        $customer,

                    'customerId' =>
                        $customerId,

                    'piutangReport' =>
                        $piutangReport,

                    'pembayaranReport' =>
                        $pembayaranReport,

                    'summary' =>
                        $summary,
                ]
            );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Print Laporan Customer Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    ENVIRONMENT === 'development'
                        ? $e->getMessage()
                        : 'Print laporan customer gagal diproses.'
                );
        }
    }

    /**
     * ----------------------------------------------------------------------
     * EXPORT PDF LAPORAN CUSTOMER
     * ----------------------------------------------------------------------
     *
     * GET /laporan/customer/pdf
     */
    public function customerPdf(): ResponseInterface
    {
        try {

            $customerId =
                $this->getCustomerId();


            if ($customerId === null) {

                return $this->response
                    ->setStatusCode(400)
                    ->setBody(
                        'Customer belum dipilih.'
                    );
            }


            /*
            * --------------------------------------------------------------
            * CUSTOMER
            * --------------------------------------------------------------
            */

            $customer =
                $this->customerModel
                    ->withDeleted()
                    ->find(
                        $customerId
                    );


            if ($customer === null) {

                return $this->response
                    ->setStatusCode(404)
                    ->setBody(
                        'Customer tidak ditemukan.'
                    );
            }


            /*
            * --------------------------------------------------------------
            * REPORT
            * --------------------------------------------------------------
            */

            $customerReport =
                $this->buildCustomerReport(
                    $customerId
                );


            $piutangReport =
                $customerReport['piutang'];


            $pembayaranReport =
                $customerReport['pembayaran'];


            /*
            * --------------------------------------------------------------
            * SUMMARY
            * --------------------------------------------------------------
            */

            $summary =
                $this->buildCustomerSummary(
                    $piutangReport,
                    $pembayaranReport
                );


            /*
            * --------------------------------------------------------------
            * HTML
            * --------------------------------------------------------------
            */

            $html =
                view(
                    'laporan/customer/print/index',
                    [
                        'title' =>
                            'Laporan Customer',

                        'customer' =>
                            $customer,

                        'customerId' =>
                            $customerId,

                        'piutangReport' =>
                            $piutangReport,

                        'pembayaranReport' =>
                            $pembayaranReport,

                        'summary' =>
                            $summary,
                    ]
                );


            /*
            * --------------------------------------------------------------
            * DOMPDF OPTIONS
            * --------------------------------------------------------------
            */

            $options =
                new \Dompdf\Options();


            $options->set(
                'isHtml5ParserEnabled',
                true
            );


            $options->set(
                'isRemoteEnabled',
                true
            );


            /*
            * --------------------------------------------------------------
            * DOMPDF
            * --------------------------------------------------------------
            */

            $dompdf =
                new \Dompdf\Dompdf(
                    $options
                );


            $dompdf->loadHtml(
                $html,
                'UTF-8'
            );


            /*
            * --------------------------------------------------------------
            * PAPER
            * --------------------------------------------------------------
            *
            * Customer Statement cukup lebar karena memiliki dua tabel.
            */

            $dompdf->setPaper(
                'A4',
                'landscape'
            );


            /*
            * --------------------------------------------------------------
            * RENDER
            * --------------------------------------------------------------
            */

            $dompdf->render();


            /*
            * --------------------------------------------------------------
            * FILE NAME
            * --------------------------------------------------------------
            */

            $namaCustomer =
                $customer['nama']
                ?? 'customer';


            $filename =
                'laporan-customer-'
                . url_title(
                    $namaCustomer,
                    '-',
                    true
                )
                . '-'
                . date('Ymd-His')
                . '.pdf';


            /*
            * --------------------------------------------------------------
            * DOWNLOAD
            * --------------------------------------------------------------
            *
            * attachment = langsung download.
            */

            return $this->response
                ->setContentType(
                    'application/pdf'
                )
                ->setHeader(
                    'Content-Disposition',
                    'attachment; filename="'
                    . $filename
                    . '"'
                )
                ->setBody(
                    $dompdf->output()
                );

        } catch (Throwable $e) {

            log_message(
                'error',
                'Export PDF Laporan Customer Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    ENVIRONMENT === 'development'
                        ? $e->getMessage()
                        : 'Export PDF laporan customer gagal diproses.'
                );
        }
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD CUSTOMER REPORT
     * ----------------------------------------------------------------------
     *
     * Menghasilkan:
     *
     * - Riwayat piutang customer
     * - Riwayat pembayaran customer
     */
    protected function buildCustomerReport(
        int $customerId
    ): array {

        /*
        * --------------------------------------------------------------
        * PIUTANG
        * --------------------------------------------------------------
        *
        * withDeleted() digunakan agar histori piutang yang pernah
        * di-soft-delete tetap dapat muncul.
        */

        $piutangs =
            $this->piutangModel
                ->withDeleted()
                ->getAllWithCustomer();


        $piutangReport = [];


        foreach (
            $piutangs as $piutang
        ) {

            /*
            * Hanya customer yang dipilih.
            */

            if (
                (int) (
                    $piutang['customer_id']
                    ?? 0
                )
                !== $customerId
            ) {
                continue;
            }


            /*
            * ----------------------------------------------------------
            * KONDISI PIUTANG
            * ----------------------------------------------------------
            *
            * Menggunakan tanggal hari ini sebagai posisi laporan.
            */

            $condition =
                $this->piutangMonitoringService
                    ->calculateCondition(
                        $piutang,
                        date('Y-m-d')
                    );


            /*
            * ----------------------------------------------------------
            * NILAI DASAR
            * ----------------------------------------------------------
            */

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


            $dendaBerjalan =
                (float) (
                    $condition[
                        'denda_berjalan'
                    ] ?? 0
                );


            $totalPembayaran =
                (float) (
                    $condition[
                        'total_pembayaran'
                    ] ?? 0
                );


            /*
            * ----------------------------------------------------------
            * TOTAL TAGIHAN
            * ----------------------------------------------------------
            *
            * Total Tagihan:
            *
            * Pokok + Bunga + Denda
            */

            $totalTagihan =
                $nominalPokok
                + $nominalBunga
                + $dendaBerjalan;


            /*
            * ----------------------------------------------------------
            * SISA TAGIHAN
            * ----------------------------------------------------------
            *
            * Total Tagihan - Pembayaran Valid
            */

            $sisaTagihan =
                max(
                    0,
                    $totalTagihan
                    - $totalPembayaran
                );


            /*
            * ----------------------------------------------------------
            * STATUS
            * ----------------------------------------------------------
            */

            $status =
                (
                    $condition[
                        'sudah_lunas'
                    ] ?? false
                )
                    ? 'lunas'
                    : 'belum_lunas';


            /*
            * ----------------------------------------------------------
            * GABUNGKAN
            * ----------------------------------------------------------
            */

            $piutangReport[] =
                array_merge(
                    $piutang,
                    $condition,
                    [
                        /*
                        * Total Piutang = pokok yang dipinjam.
                        */

                        'total_piutang' =>
                            $this->money(
                                $nominalPokok
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
                                $sisaTagihan
                            ),


                        'status' =>
                            $status,
                    ]
                );
        }


        /*
        * --------------------------------------------------------------
        * PEMBAYARAN
        * --------------------------------------------------------------
        *
        * Semua histori pembayaran ditampilkan:
        *
        * - valid
        * - dibatalkan
        */

        $pembayarans =
            $this->pembayaranModel
                ->getAllWithPiutang();


        $pembayaranReport = [];


        foreach (
            $pembayarans as $pembayaran
        ) {

            if (
                (int) (
                    $pembayaran[
                        'customer_id'
                    ] ?? 0
                )
                !== $customerId
            ) {
                continue;
            }


            /*
            * Snapshot transaksi pembayaran
            * tidak dihitung ulang.
            */

            $pembayaranReport[] =
                array_merge(
                    $pembayaran,
                    [
                        'total_tagihan' =>
                            $this->money(
                                $pembayaran[
                                    'total_tagihan'
                                ] ?? 0
                            ),

                        'nominal_pembayaran' =>
                            $this->money(
                                $pembayaran[
                                    'nominal_pembayaran'
                                ] ?? 0
                            ),

                        'alokasi_denda' =>
                            $this->money(
                                $pembayaran[
                                    'alokasi_denda'
                                ] ?? 0
                            ),

                        'alokasi_bunga' =>
                            $this->money(
                                $pembayaran[
                                    'alokasi_bunga'
                                ] ?? 0
                            ),

                        'alokasi_pokok' =>
                            $this->money(
                                $pembayaran[
                                    'alokasi_pokok'
                                ] ?? 0
                            ),

                        'sisa_tagihan' =>
                            $this->money(
                                $pembayaran[
                                    'sisa_tagihan'
                                ] ?? 0
                            ),
                    ]
                );
        }


        return [

            'piutang' =>
                $piutangReport,

            'pembayaran' =>
                $pembayaranReport,

        ];
    }

    /**
     * ----------------------------------------------------------------------
     * BUILD CUSTOMER SUMMARY
     * ----------------------------------------------------------------------
     */
    protected function buildCustomerSummary(
        array $piutangReport,
        array $pembayaranReport
    ): array {

        $jumlahPiutang =
            count(
                $piutangReport
            );


        $jumlahLunas = 0;

        $jumlahBelumLunas = 0;


        $totalPiutang = 0.0;

        $totalTagihan = 0.0;

        $totalPembayaran = 0.0;

        $totalSisaTagihan = 0.0;


        /*
        * --------------------------------------------------------------
        * RINGKASAN PIUTANG
        * --------------------------------------------------------------
        */

        foreach (
            $piutangReport as $row
        ) {

            $totalPiutang +=
                (float) (
                    $row[
                        'total_piutang'
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


            $totalSisaTagihan +=
                (float) (
                    $row[
                        'sisa_tagihan'
                    ] ?? 0
                );


            if (
                (
                    $row['status']
                    ?? ''
                )
                === 'lunas'
            ) {

                $jumlahLunas++;

            } else {

                $jumlahBelumLunas++;

            }
        }


        /*
        * --------------------------------------------------------------
        * PEMBAYARAN VALID
        * --------------------------------------------------------------
        *
        * Pembayaran dibatalkan tetap tampil sebagai histori,
        * tetapi tidak dihitung sebagai pembayaran aktif.
        */

        $totalPembayaranValid = 0.0;


        foreach (
            $pembayaranReport as $row
        ) {

            if (
                (
                    $row['status']
                    ?? null
                )
                === PembayaranModel::STATUS_VALID
            ) {

                $totalPembayaranValid +=
                    (float) (
                        $row[
                            'nominal_pembayaran'
                        ] ?? 0
                    );
            }
        }


        return [

            'jumlah_piutang' =>
                $jumlahPiutang,

            'jumlah_lunas' =>
                $jumlahLunas,

            'jumlah_belum_lunas' =>
                $jumlahBelumLunas,

            /*
            * Total uang pokok yang pernah dipinjam.
            */

            'total_piutang' =>
                $this->money(
                    $totalPiutang
                ),

            /*
            * Pokok + bunga + denda berjalan.
            */

            'total_tagihan' =>
                $this->money(
                    $totalTagihan
                ),

            /*
            * Hanya pembayaran VALID.
            */

            'total_pembayaran' =>
                $this->money(
                    $totalPembayaranValid
                ),

            /*
            * Total Tagihan - Pembayaran Valid.
            */

            'sisa_tagihan' =>
                $this->money(
                    $totalSisaTagihan
                ),

        ];
    }
}