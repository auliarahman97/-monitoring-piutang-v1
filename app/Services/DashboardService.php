<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\CustomerModel;
use App\Models\PembayaranModel;
use App\Models\PiutangModel;
use Throwable;

class DashboardService
{
    /**
     * ----------------------------------------------------------------------
     * Models
     * ----------------------------------------------------------------------
     */

    protected CustomerModel $customerModel;

    protected PiutangModel $piutangModel;

    protected PembayaranModel $pembayaranModel;


    /**
     * ----------------------------------------------------------------------
     * Services
     * ----------------------------------------------------------------------
     */

    protected PiutangMonitoringService $piutangMonitoringService;


    /**
     * ----------------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------------
     */

    public function __construct(
        ?CustomerModel $customerModel = null,
        ?PiutangModel $piutangModel = null,
        ?PembayaranModel $pembayaranModel = null,
        ?PiutangMonitoringService $piutangMonitoringService = null
    ) {
        $this->customerModel =
            $customerModel
            ?? new CustomerModel();


        $this->piutangModel =
            $piutangModel
            ?? new PiutangModel();


        $this->pembayaranModel =
            $pembayaranModel
            ?? new PembayaranModel();


        $this->piutangMonitoringService =
            $piutangMonitoringService
            ?? new PiutangMonitoringService(
                $this->piutangModel,
                $this->pembayaranModel
            );
    }


    /**
     * ----------------------------------------------------------------------
     * GET DASHBOARD DATA
     * ----------------------------------------------------------------------
     *
     * Satu pintu untuk seluruh kebutuhan Dashboard.
     *
     * Controller cukup memanggil:
     *
     * $this->dashboardService->getDashboardData()
     */
    public function getDashboardData(): array
    {
        $tanggalLaporan =
            date('Y-m-d');


        /*
         * --------------------------------------------------------------
         * PIUTANG MONITORING
         * --------------------------------------------------------------
         *
         * Seluruh kondisi finansial piutang berasal dari
         * PiutangMonitoringService.
         */

        $piutangReport =
            $this->piutangMonitoringService
                ->getCurrentReport(
                    $tanggalLaporan
                );


        /*
         * --------------------------------------------------------------
         * SUMMARY
         * --------------------------------------------------------------
         */

        $summary =
            $this->piutangMonitoringService
                ->buildSummary(
                    $piutangReport
                );


        /*
         * --------------------------------------------------------------
         * CUSTOMER AKTIF
         * --------------------------------------------------------------
         *
         * Dashboard hanya menghitung customer aktif.
         *
         * status = 1
         */

        $totalCustomer =
            $this->customerModel
                ->where(
                    'status',
                    1
                )
                ->countAllResults();


        /*
         * --------------------------------------------------------------
         * OVERDUE
         * --------------------------------------------------------------
         *
         * Maksimal 5 data.
         */

        $overdue =
            $this->piutangMonitoringService
                ->getOverdue(
                    $piutangReport,
                    5
                );


        /*
         * --------------------------------------------------------------
         * LATEST PIUTANG
         * --------------------------------------------------------------
         *
         * Maksimal 5 data.
         */

        $latestPiutang =
            $this->getLatestPiutang(
                $piutangReport,
                5
            );


        /*
         * --------------------------------------------------------------
         * LATEST PEMBAYARAN
         * --------------------------------------------------------------
         *
         * Maksimal 5 data.
         */

        $latestPembayaran =
            $this->getLatestPembayaran(
                5
            );


        /*
         * --------------------------------------------------------------
         * SUMMARY DASHBOARD
         * --------------------------------------------------------------
         *
         * Customer aktif sengaja diletakkan di DashboardService,
         * karena ini merupakan KPI master customer.
         *
         * Sedangkan seluruh nilai piutang tetap berasal dari
         * PiutangMonitoringService.
         */

        $summary[
            'total_customer'
        ] =
            $totalCustomer;


        /*
         * --------------------------------------------------------------
         * RETURN
         * --------------------------------------------------------------
         */

        return [

            'tanggal_laporan' =>
                $tanggalLaporan,

            'summary' =>
                $summary,

            'overdue' =>
                $overdue,

            'latest_piutang' =>
                $latestPiutang,

            'latest_pembayaran' =>
                $latestPembayaran,
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * GET LATEST PIUTANG
     * ----------------------------------------------------------------------
     *
     * Data piutang sudah berasal dari PiutangMonitoringService,
     * sehingga nilai total tagihan, sisa tagihan, dan status
     * sudah merupakan hasil business rule yang sama dengan laporan.
     */
    protected function getLatestPiutang(
        array $report,
        int $limit = 5
    ): array {

        /*
         * getCurrentReport() mengikuti urutan PiutangModel:
         *
         * piutang.id DESC
         *
         * sehingga data pertama adalah piutang terbaru.
         */

        return array_slice(
            $report,
            0,
            max(
                0,
                $limit
            )
        );
    }


    /**
     * ----------------------------------------------------------------------
     * GET LATEST PEMBAYARAN
     * ----------------------------------------------------------------------
     *
     * Mengambil histori pembayaran terbaru.
     *
     * Pembayaran VALID maupun DIBATALKAN tetap dapat muncul
     * karena Dashboard menampilkan aktivitas transaksi,
     * bukan saldo pembayaran.
     */
    protected function getLatestPembayaran(
        int $limit = 5
    ): array {

        $limit =
            max(
                0,
                $limit
            );


        if ($limit === 0) {
            return [];
        }


        $pembayaran =
            $this->pembayaranModel
                ->getAllWithPiutang();


        return array_slice(
            $pembayaran,
            0,
            $limit
        );
    }
}