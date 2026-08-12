<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\DashboardService;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Dashboard extends BaseController
{
    /**
     * ----------------------------------------------------------------------
     * Services
     * ----------------------------------------------------------------------
     */

    protected DashboardService $dashboardService;


    /**
     * ----------------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------------
     */

    public function __construct()
    {
        $this->dashboardService =
            new DashboardService();
    }


    /**
     * ----------------------------------------------------------------------
     * Index
     * ----------------------------------------------------------------------
     *
     * Menampilkan Dashboard utama aplikasi.
     *
     * Seluruh perhitungan dan pengambilan data Dashboard
     * dilakukan oleh DashboardService.
     */
    public function index(): string|ResponseInterface
    {
        try {

            /*
             * --------------------------------------------------------------
             * Dashboard Data
             * --------------------------------------------------------------
             *
             * Controller tidak melakukan query atau business logic.
             */

            $data =
                $this->dashboardService
                    ->getDashboardData();


            /*
             * --------------------------------------------------------------
             * View
             * --------------------------------------------------------------
             */

            return view(
                'dashboard/index',
                [
                    'title' => 'Dashboard',

                    'tanggalLaporan' =>
                        $data['tanggal_laporan'],

                    'summary' =>
                        $data['summary'],

                    'overdue' =>
                        $data['overdue'],

                    'latestPiutang' =>
                        $data['latest_piutang'],

                    'latestPembayaran' =>
                        $data['latest_pembayaran'],
                ]
            );

        } catch (Throwable $e) {

            /*
             * --------------------------------------------------------------
             * Error Handling
             * --------------------------------------------------------------
             */

            log_message(
                'error',
                'Dashboard Error: {message}',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );


            /*
             * Jangan tampilkan detail exception
             * pada production.
             */

            return $this->response
                ->setStatusCode(500)
                ->setBody(
                    ENVIRONMENT === 'development'
                        ? 'Dashboard Error: ' . $e->getMessage()
                        : 'Dashboard gagal dimuat.'
                );
        }
    }
}