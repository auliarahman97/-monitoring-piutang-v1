<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CustomerModel;
use App\Models\PembayaranModel;
use App\Models\PiutangModel;
use App\Services\PaymentService;
use App\Services\PiutangMonitoringService;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\HTTP\ResponseInterface;
use Throwable;

class Pembayaran extends BaseController
{
    /**
     * ----------------------------------------------------------------------
     * Models
     * ----------------------------------------------------------------------
     */

    protected PembayaranModel $pembayaranModel;

    protected PiutangModel $piutangModel;

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
        $this->pembayaranModel = new PembayaranModel();

        $this->piutangModel = new PiutangModel();

        $this->customerModel = new CustomerModel();

        $this->paymentService = new PaymentService(
            $this->piutangModel,
            $this->pembayaranModel
        );

        $this->piutangMonitoringService = new PiutangMonitoringService(
            $this->piutangModel,
            $this->pembayaranModel
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Index
     * ----------------------------------------------------------------------
     *
     * Menampilkan seluruh histori pembayaran.
     *
     * Pembayaran VALID maupun DIBATALKAN tetap ditampilkan.
     */
    public function index(): string
    {
        $pembayaran =
            $this->pembayaranModel
                ->getAllWithPiutang();

        return view('pembayaran/index', [
            'title'      => 'Data Pembayaran',
            'pembayaran' => $pembayaran,
        ]);
    }


    /**
     * ----------------------------------------------------------------------
     * Create
     * ----------------------------------------------------------------------
     *
     * Menampilkan form pembayaran.
     *
     * Hanya piutang yang masih memiliki sisa tagihan
     * yang boleh dipilih.
     */
    public function create(): string
    {
        $customers =
            $this->customerModel
                ->where('status', 1)
                ->orderBy('nama', 'ASC')
                ->findAll();

        return view('pembayaran/create', [
            'title'     => 'Tambah Pembayaran',
            'customers' => $customers,
            'today'     => date('Y-m-d'),
        ]);
    }

    /**
     * ----------------------------------------------------------------------
     * Piutang By Customer
     * ----------------------------------------------------------------------
     */
    public function piutangByCustomer(
        int $customerId
    ): ResponseInterface {

        if ($customerId <= 0) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => 'Customer tidak valid.',
                    'data'    => [],
                ]);
        }

        try {

            /*
            * ================================================================
            * Ambil kondisi seluruh piutang
            * ================================================================
            *
            * Perhitungan sisa tagihan menggunakan
            * PiutangMonitoringService sebagai single source of truth.
            */

            $report =
                $this->piutangMonitoringService
                    ->getCurrentReport();


            /*
            * ================================================================
            * Filter berdasarkan customer dan outstanding
            * ================================================================
            */

            $piutang = [];

            foreach ($report as $row) {

                if (
                    (int) (
                        $row['customer_id']
                        ?? 0
                    ) !== $customerId
                ) {
                    continue;
                }


                /*
                * Hanya piutang yang masih memiliki
                * sisa tagihan yang boleh dipilih.
                */

                if (
                    (float) (
                        $row['sisa_tagihan']
                        ?? 0
                    ) <= 0
                ) {
                    continue;
                }


                $piutang[] = [
                    'id' =>
                        (int) $row['id'],

                    'nomor_piutang' =>
                        $row['nomor_piutang'],

                    'customer_id' =>
                        (int) $row['customer_id'],

                    'tanggal_piutang' =>
                        $row['tanggal_piutang'],

                    'tanggal_jatuh_tempo' =>
                        $row['tanggal_jatuh_tempo'],

                    'nominal_pokok' =>
                        $row['nominal_pokok'],

                    'nominal_bunga' =>
                        $row['nominal_bunga'],
                ];
            }


            /*
            * ================================================================
            * Urutan
            * ================================================================
            */

            usort(
                $piutang,
                static function (
                    array $a,
                    array $b
                ): int {

                    $tanggalA =
                        $a['tanggal_jatuh_tempo']
                        ?? '';

                    $tanggalB =
                        $b['tanggal_jatuh_tempo']
                        ?? '';

                    if ($tanggalA === $tanggalB) {

                        return
                            (int) $a['id']
                            <=>
                            (int) $b['id'];
                    }

                    return strcmp(
                        $tanggalA,
                        $tanggalB
                    );
                }
            );


            return $this->response->setJSON([
                'success' => true,
                'data'    => $piutang,
            ]);

        } catch (Throwable $e) {

            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'data'    => [],
                ]);
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Preview
     * ----------------------------------------------------------------------
     *
     * Menghitung kondisi tagihan pada tanggal pembayaran.
     *
     * Endpoint ini dipanggil AJAX dari form pembayaran.
     *
     * Contoh:
     *
     * GET /pembayaran/preview/12?tanggal_pembayaran=2026-08-09
     */
    public function preview(int $piutangId): ResponseInterface
    {
        $tanggalPembayaran =
            trim(
                (string) $this->request->getGet(
                    'tanggal_pembayaran'
                )
            );

        if ($tanggalPembayaran === '') {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' =>
                        'Tanggal pembayaran wajib diisi.',
                ]);
        }

        try {
            $result =
                $this->paymentService->preview(
                    $piutangId,
                    $tanggalPembayaran
                );

            return $this->response->setJSON([
                'success' => true,
                'data'    => $result,
            ]);

        } catch (Throwable $e) {

            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'success' => false,
                    'message' => $e->getMessage(),
                ]);
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Store
     * ----------------------------------------------------------------------
     *
     * Menyimpan pembayaran baru.
     *
     * Pembayaran tidak boleh diedit setelah tersimpan.
     */
    public function store(): RedirectResponse
    {
        /*
         * --------------------------------------------------------------
         * Input
         * --------------------------------------------------------------
         */

        $piutangId =
            (int) $this->request->getPost(
                'piutang_id'
            );

        $tanggalPembayaran =
            trim(
                (string) $this->request->getPost(
                    'tanggal_pembayaran'
                )
            );

        $nominalPembayaran =
            $this->normalizeMoney(
                $this->request->getPost(
                    'nominal_pembayaran'
                )
            );

        $keterangan =
            trim(
                (string) $this->request->getPost(
                    'keterangan'
                )
            );


        /*
         * --------------------------------------------------------------
         * Basic Validation
         * --------------------------------------------------------------
         */

        $rules = [
            'piutang_id' => [
                'label' => 'Piutang',
                'rules' =>
                    'required|integer|greater_than[0]',
            ],

            'tanggal_pembayaran' => [
                'label' => 'Tanggal Pembayaran',
                'rules' =>
                    'required|valid_date[Y-m-d]',
            ],

            'nominal_pembayaran' => [
                'label' => 'Nominal Pembayaran',
                'rules' =>
                    'required|decimal|greater_than[0]',
            ],

            'keterangan' => [
                'label' => 'Keterangan',
                'rules' =>
                    'permit_empty',
            ],
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'errors',
                    $this->validator->getErrors()
                );
        }


        /*
         * --------------------------------------------------------------
         * Current User
         * --------------------------------------------------------------
         */

        $createdBy =
            $this->currentUserId();


        /*
         * --------------------------------------------------------------
         * Business Process
         * --------------------------------------------------------------
         */

        try {

            $paymentId =
                $this->paymentService->createPayment(
                    $piutangId,
                    $tanggalPembayaran,
                    $nominalPembayaran,
                    $createdBy,
                    $keterangan !== ''
                        ? $keterangan
                        : null
                );


            /*
             * ----------------------------------------------------------
             * Success
             * ----------------------------------------------------------
             */

            $payment =
                $this->pembayaranModel->find(
                    $paymentId
                );

            return redirect()
                ->to(
                    site_url(
                        'pembayaran/detail/'
                        . $paymentId
                    )
                )
                ->with(
                    'success',
                    'Pembayaran '
                    . ($payment['nomor_pembayaran'] ?? '')
                    . ' berhasil disimpan.'
                );

        } catch (Throwable $e) {

            /*
             * ----------------------------------------------------------
             * Error
             * ----------------------------------------------------------
             */

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Detail
     * ----------------------------------------------------------------------
     */
    public function detail(int $id): string|RedirectResponse
    {
        $pembayaran =
            $this->pembayaranModel
                ->getWithPiutang($id);

        if ($pembayaran === null) {
            return redirect()
                ->to(
                    site_url('pembayaran')
                )
                ->with(
                    'error',
                    'Data pembayaran tidak ditemukan.'
                );
        }

        return view('pembayaran/detail', [
            'title'      => 'Detail Pembayaran',
            'pembayaran' => $pembayaran,
        ]);
    }


    /**
     * ----------------------------------------------------------------------
     * Cancel
     * ----------------------------------------------------------------------
     *
     * Membatalkan pembayaran.
     *
     * Record pembayaran TIDAK dihapus.
     */
    public function cancel(int $id): RedirectResponse
    {
        $reason =
            trim(
                (string) $this->request->getPost(
                    'alasan'
                )
            );

        if ($reason === '') {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'Alasan pembatalan wajib diisi.'
                );
        }

        try {

            $success =
                $this->paymentService->cancelPayment(
                    $id,
                    $this->currentUserId(),
                    $reason
                );

            if (! $success) {
                return redirect()
                    ->back()
                    ->with(
                        'error',
                        'Pembayaran gagal dibatalkan.'
                    );
            }

            return redirect()
                ->to(
                    site_url(
                        'pembayaran/detail/' . $id
                    )
                )
                ->with(
                    'success',
                    'Pembayaran berhasil dibatalkan.'
                );

        } catch (Throwable $e) {

            return redirect()
                ->back()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }


    /**
     * ----------------------------------------------------------------------
     * Normalize Money
     * ----------------------------------------------------------------------
     *
     * Menangani input:
     *
     * 1000000
     * 1.000.000
     * 1,000,000
     * 1000000.50
     *
     * menjadi nilai numerik yang aman.
     */
    protected function normalizeMoney(
        mixed $value
    ): float {
        if (
            $value === null
            || $value === ''
        ) {
            return 0.0;
        }

        $value =
            trim((string) $value);

        /*
         * Jika menggunakan format Indonesia:
         *
         * 1.500.000,50
         *
         * ubah menjadi:
         *
         * 1500000.50
         */

        if (
            str_contains($value, ',')
            && str_contains($value, '.')
        ) {
            $value =
                str_replace(
                    '.',
                    '',
                    $value
                );

            $value =
                str_replace(
                    ',',
                    '.',
                    $value
                );

            return (float) $value;
        }

        /*
         * 1.500.000
         */

        if (
            substr_count($value, '.') > 1
        ) {
            return (float) str_replace(
                '.',
                '',
                $value
            );
        }

        /*
         * 1,500,000
         */

        if (
            substr_count($value, ',') > 1
        ) {
            return (float) str_replace(
                ',',
                '',
                $value
            );
        }

        /*
         * 1.500
         *
         * Untuk nominal rupiah tanpa desimal,
         * anggap titik sebagai separator ribuan.
         */

        if (
            preg_match(
                '/^\d{1,3}(\.\d{3})$/',
                $value
            )
        ) {
            return (float) str_replace(
                '.',
                '',
                $value
            );
        }

        /*
         * 1,500
         */

        if (
            preg_match(
                '/^\d{1,3}(,\d{3})$/',
                $value
            )
        ) {
            return (float) str_replace(
                ',',
                '',
                $value
            );
        }

        return (float) $value;
    }
}