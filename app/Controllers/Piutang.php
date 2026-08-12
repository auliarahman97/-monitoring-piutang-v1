<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\AturanDendaModel;
use App\Models\AturanDendaVersiModel;
use App\Models\CustomerModel;
use App\Models\PiutangModel;
use App\Services\PiutangMonitoringService;
use CodeIgniter\HTTP\RedirectResponse;

class Piutang extends BaseController
{
    /**
     * ----------------------------------------------------------------------
     * Models
     * ----------------------------------------------------------------------
     */

    protected PiutangModel $piutangModel;

    protected CustomerModel $customerModel;

    protected AturanDendaModel $aturanDendaModel;

    protected AturanDendaVersiModel $aturanDendaVersiModel;

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

        $this->customerModel =
            new CustomerModel();

        $this->aturanDendaModel =
            new AturanDendaModel();

        $this->aturanDendaVersiModel =
            new AturanDendaVersiModel();
        
        $this->piutangMonitoringService =
            new PiutangMonitoringService(
                $this->piutangModel
            );
    }


    /**
     * ----------------------------------------------------------------------
     * Index
     * ----------------------------------------------------------------------
     *
     * Menampilkan daftar piutang.
     */

    public function index(): string
    {
        $piutang =
            $this->piutangMonitoringService
                ->getCurrentReport();

        return view(
            'piutang/index',
            [
                'title' =>
                    'Piutang',

                'piutang' =>
                    $piutang,
            ]
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Create
     * ----------------------------------------------------------------------
     *
     * Menampilkan form tambah piutang.
     *
     * Versi aturan denda tidak dipilih manual.
     * Sistem akan menentukan versi berdasarkan tanggal piutang
     * ketika data disimpan.
     */

    public function create(): string
    {
        $customers =
            $this->customerModel
                ->where(
                    'status',
                    1
                )
                ->orderBy(
                    'nama',
                    'ASC'
                )
                ->findAll();

        return view(
            'piutang/create',
            [
                'title' =>
                    'Tambah Piutang',

                'customers' =>
                    $customers,
            ]
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Store
     * ----------------------------------------------------------------------
     *
     * Menyimpan piutang baru.
     *
     * Alur:
     *
     * 1. Validasi input dasar
     * 2. Validasi customer
     * 3. Validasi tanggal
     * 4. Cari versi denda berdasarkan tanggal piutang
     * 5. Cari aturan denda berdasarkan nominal
     * 6. Snapshot aturan denda
     * 7. Hitung bunga
     * 8. Generate nomor piutang
     * 9. Simpan transaksi
     */

    public function store(): RedirectResponse
    {
        $data =
            $this->getFormData();


        /*
        |--------------------------------------------------------------------------
        | Validasi input dasar
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->validate(
                $this->getInputValidationRules()
            )
        ) {
            return $this->backWithInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi Customer
        |--------------------------------------------------------------------------
        */

        $customer =
            $this->customerModel
                ->where(
                    'id',
                    $data['customer_id']
                )
                ->where(
                    'status',
                    1
                )
                ->first();

        if ($customer === null) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Customer tidak ditemukan atau sudah tidak aktif.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi tanggal
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->piutangModel->validateDueDate(
                $data['tanggal_piutang'],
                $data['tanggal_jatuh_tempo']
            )
        ) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal piutang.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cari versi denda yang berlaku
        |--------------------------------------------------------------------------
        */

        $versi =
            $this->aturanDendaVersiModel
                ->getApplicableVersion(
                    $data['tanggal_piutang']
                );

        if ($versi === null) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Tidak ditemukan versi aturan denda yang berlaku untuk tanggal piutang tersebut.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cari aturan denda berdasarkan nominal
        |--------------------------------------------------------------------------
        */

        $aturanDenda =
            $this->aturanDendaModel
                ->getApplicableRule(
                    (int) $versi['id'],
                    (float) $data['nominal_pokok']
                );

        if ($aturanDenda === null) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Tidak ditemukan rentang aturan denda yang sesuai dengan nominal piutang dalam versi '
                    . ($versi['kode_versi'] ?? 'tersebut')
                    . '.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Snapshot versi & aturan denda
        |--------------------------------------------------------------------------
        |
        | Piutang menyimpan versi aturan yang digunakan saat transaksi dibuat.
        | Nilai denda juga disnapshot agar histori tidak berubah ketika
        | master aturan denda diperbarui di kemudian hari.
        |--------------------------------------------------------------------------
        */

        $data['denda_versi_id'] =
            (int) $versi['id'];

        $data['persentase_denda'] =
            (float) $aturanDenda['persentase_denda'];

        $data['periode_denda_hari'] =
            (int) $aturanDenda['periode_hari'];

        $data['maksimal_denda_persen'] =
            (float) $aturanDenda['maksimal_denda_persen'];


        /*
        |--------------------------------------------------------------------------
        | Hitung bunga
        |--------------------------------------------------------------------------
        */

        $data['nominal_bunga'] =
            $this->piutangModel
                ->calculateInterest(
                    $data['nominal_pokok'],
                    $data['persentase_bunga']
                );


        /*
        |--------------------------------------------------------------------------
        | Generate nomor piutang
        |--------------------------------------------------------------------------
        */

        $data['nomor_piutang'] =
            $this->generateNomorPiutang();


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $data['created_by'] =
            $this->currentUserId();


        /*
        |--------------------------------------------------------------------------
        | Simpan dengan transaction
        |--------------------------------------------------------------------------
        */

        $db =
            db_connect();

        $db->transStart();

        $inserted =
            $this->piutangModel->insert(
                $data,
                true
            );

        if ($inserted === false) {

            $db->transRollback();

            return $this->backWithInput();
        }

        $db->transComplete();


        if (! $db->transStatus()) {

            return $this->redirectError(
                'piutang',
                'Data piutang gagal disimpan.'
            );
        }


        return $this->redirectSuccess(
            'piutang',
            'Data piutang berhasil ditambahkan.'
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Edit
     * ----------------------------------------------------------------------
     *
     * Menampilkan form edit piutang.
     *
     * Customer tetap mengikuti customer yang sudah melekat
     * pada piutang.
     *
     * Versi denda juga tetap mengikuti versi yang tersimpan.
     */

    public function edit(
        int $id
    ): RedirectResponse|string {

        $piutang =
            $this->piutangModel
                ->getWithCustomer(
                    $id
                );

        if ($piutang === null) {

            return $this->redirectError(
                'piutang',
                'Data piutang tidak ditemukan.'
            );
        }


        $customers =
            $this->customerModel
                ->where(
                    'status',
                    1
                )
                ->orderBy(
                    'nama',
                    'ASC'
                )
                ->findAll();


        return view(
            'piutang/edit',
            [
                'title' =>
                    'Edit Piutang',

                'piutang' =>
                    $piutang,

                'customers' =>
                    $customers,
            ]
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Update
     * ----------------------------------------------------------------------
     *
     * Memperbarui piutang.
     *
     * PENTING:
     *
     * denda_versi_id tidak pernah dipindahkan ke versi lain.
     *
     * Jika tanggal piutang diubah, tanggal tersebut harus tetap
     * berada dalam versi denda yang sama.
     *
     * Jika nominal berubah, sistem akan mencari rentang nominal
     * baru dalam versi yang sama.
     */

    public function update(
        int $id
    ): RedirectResponse {

        $piutang =
            $this->piutangModel
                ->find($id);

        if ($piutang === null) {

            return $this->redirectError(
                'piutang',
                'Data piutang tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil data form
        |--------------------------------------------------------------------------
        */

        $data =
            $this->getFormData();


        /*
        |--------------------------------------------------------------------------
        | Customer tetap
        |--------------------------------------------------------------------------
        |
        | Customer tidak boleh berpindah pada transaksi piutang
        | yang sudah dibuat.
        |--------------------------------------------------------------------------
        */

        $data['customer_id'] =
            $piutang['customer_id'];


        /*
        |--------------------------------------------------------------------------
        | Nomor piutang tetap
        |--------------------------------------------------------------------------
        */

        $data['nomor_piutang'] =
            $piutang['nomor_piutang'];


        /*
        |--------------------------------------------------------------------------
        | Validasi dasar
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->validate(
                $this->getInputValidationRules()
            )
        ) {
            return $this->backWithInput();
        }


        /*
        |--------------------------------------------------------------------------
        | Validasi tanggal jatuh tempo
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->piutangModel->validateDueDate(
                $data['tanggal_piutang'],
                $data['tanggal_jatuh_tempo']
            )
        ) {
            return $this->backWithInput()
                ->with(
                    'error',
                    'Tanggal jatuh tempo tidak boleh lebih awal dari tanggal piutang.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Ambil versi denda yang sudah melekat
        |--------------------------------------------------------------------------
        */

        $versiId =
            (int) (
                $piutang['denda_versi_id']
                ?? 0
            );

        if ($versiId <= 0) {

            return $this->redirectError(
                'piutang',
                'Versi aturan denda pada piutang tidak valid.'
            );
        }


        $versi =
            $this->aturanDendaVersiModel
                ->getById(
                    $versiId
                );

        if ($versi === null) {

            return $this->redirectError(
                'piutang',
                'Versi aturan denda yang digunakan piutang tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Pastikan tanggal tetap berada dalam versi yang sama
        |--------------------------------------------------------------------------
        |
        | Kita tidak menggunakan helper tambahan.
        |
        | Cukup cari versi yang berlaku berdasarkan tanggal baru,
        | kemudian bandingkan ID-nya dengan versi yang melekat
        | pada piutang.
        |--------------------------------------------------------------------------
        */

        $versiTanggalBaru =
            $this->aturanDendaVersiModel
                ->getApplicableVersion(
                    $data['tanggal_piutang']
                );

        if (
            $versiTanggalBaru === null
            ||
            (int) $versiTanggalBaru['id']
                !== $versiId
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Tanggal piutang harus tetap berada dalam periode versi denda '
                    . ($versi['kode_versi'] ?? 'yang digunakan')
                    . '. Piutang tidak dapat berpindah versi aturan denda.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Cari rentang nominal dalam versi yang sama
        |--------------------------------------------------------------------------
        */

        $aturanDenda =
            $this->aturanDendaModel
                ->getApplicableRule(
                    $versiId,
                    (float) $data['nominal_pokok']
                );

        if ($aturanDenda === null) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Nominal piutang tidak memiliki rentang aturan denda dalam versi '
                    . ($versi['kode_versi'] ?? 'yang digunakan')
                    . '.'
                );
        }


        /*
        |--------------------------------------------------------------------------
        | Pertahankan versi
        |--------------------------------------------------------------------------
        */

        $data['denda_versi_id'] =
            $versiId;


        /*
        |--------------------------------------------------------------------------
        | Update snapshot aturan denda
        |--------------------------------------------------------------------------
        */

        $data['persentase_denda'] =
            (float) $aturanDenda['persentase_denda'];

        $data['periode_denda_hari'] =
            (int) $aturanDenda['periode_hari'];

        $data['maksimal_denda_persen'] =
            (float) $aturanDenda['maksimal_denda_persen'];


        /*
        |--------------------------------------------------------------------------
        | Hitung ulang bunga
        |--------------------------------------------------------------------------
        */

        $data['nominal_bunga'] =
            $this->piutangModel
                ->calculateInterest(
                    $data['nominal_pokok'],
                    $data['persentase_bunga']
                );


        /*
        |--------------------------------------------------------------------------
        | Audit
        |--------------------------------------------------------------------------
        */

        $data['updated_by'] =
            $this->currentUserId();


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->piutangModel->update(
                $id,
                $data
            )
        ) {

            return $this->backWithInput()
                ->with(
                    'error',
                    'Data piutang gagal diperbarui.'
                );
        }


        return $this->redirectSuccess(
            'piutang',
            'Data piutang berhasil diperbarui.'
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Delete
     * ----------------------------------------------------------------------
     *
     * Soft delete piutang.
     *
     * Pembayaran yang sudah ada akan kita jadikan pembatas
     * pada tahap audit/refactor berikutnya apabila diperlukan.
     */

    public function delete(
        int $id
    ): RedirectResponse {

        $piutang =
            $this->piutangModel
                ->find($id);

        if ($piutang === null) {

            return $this->redirectError(
                'piutang',
                'Data piutang tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Audit delete
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->piutangModel->update(
                $id,
                [
                    'deleted_by' =>
                        $this->currentUserId(),
                ]
            );

        if (! $updated) {

            return $this->redirectError(
                'piutang',
                'Data piutang gagal dihapus.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Soft delete
        |--------------------------------------------------------------------------
        */

        if (
            ! $this->piutangModel->delete(
                $id
            )
        ) {

            return $this->redirectError(
                'piutang',
                'Data piutang gagal dihapus.'
            );
        }


        return $this->redirectSuccess(
            'piutang',
            'Data piutang berhasil dihapus.'
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Detail
     * ----------------------------------------------------------------------
     *
     * Menampilkan detail piutang beserta:
     *
     * - Customer
     * - Versi denda
     * - Snapshot denda
     * - Denda berjalan
     * - Total piutang
     */

    public function detail(
        int $id
    ): string|RedirectResponse {

        $piutang =
            $this->piutangModel
                ->getWithCustomer(
                    $id
                );

        if ($piutang === null) {

            return $this->redirectError(
                'piutang',
                'Data piutang tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Denda berjalan
        |--------------------------------------------------------------------------
        */

        $dendaBerjalan =
            $this->piutangModel
                ->calculatePenalty(
                    (float) $piutang['nominal_pokok'],
                    $piutang['tanggal_jatuh_tempo'],
                    date('Y-m-d'),
                    (float) $piutang['persentase_denda'],
                    (int) $piutang['periode_denda_hari'],
                    (float) $piutang['maksimal_denda_persen']
                );


        /*
        |--------------------------------------------------------------------------
        | Total piutang
        |--------------------------------------------------------------------------
        */

        $nominalPokok =
            (float) $piutang['nominal_pokok'];

        $nominalBunga =
            (float) $piutang['nominal_bunga'];

        $totalPiutang =
            $nominalPokok
            + $nominalBunga
            + $dendaBerjalan;


        return view(
            'piutang/detail',
            [
                'title' =>
                    'Detail Piutang',

                'piutang' =>
                    $piutang,

                'dendaBerjalan' =>
                    $dendaBerjalan,

                'totalPiutang' =>
                    $totalPiutang,
            ]
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Form Data
     * ----------------------------------------------------------------------
     */

    protected function getFormData(): array
    {
        return [
            'customer_id' =>
                $this->request
                    ->getPost(
                        'customer_id'
                    ),

            'tanggal_piutang' =>
                $this->request
                    ->getPost(
                        'tanggal_piutang'
                    ),

            'tanggal_jatuh_tempo' =>
                $this->request
                    ->getPost(
                        'tanggal_jatuh_tempo'
                    ),

            'nominal_pokok' =>
                $this->request
                    ->getPost(
                        'nominal_pokok'
                    ),

            'persentase_bunga' =>
                $this->request
                    ->getPost(
                        'persentase_bunga'
                    ),

            'keterangan' =>
                trim(
                    (string) $this->request
                        ->getPost(
                            'keterangan'
                        )
                ),
        ];
    }


    /**
     * ----------------------------------------------------------------------
     * Validation Rules
     * ----------------------------------------------------------------------
     */

    protected function getInputValidationRules(): array
    {
        return [

            'customer_id' => [
                'label' =>
                    'Customer',

                'rules' =>
                    'required|integer|greater_than[0]',
            ],

            'tanggal_piutang' => [
                'label' =>
                    'Tanggal Piutang',

                'rules' =>
                    'required|valid_date[Y-m-d]',
            ],

            'tanggal_jatuh_tempo' => [
                'label' =>
                    'Tanggal Jatuh Tempo',

                'rules' =>
                    'required|valid_date[Y-m-d]',
            ],

            'nominal_pokok' => [
                'label' =>
                    'Nominal Pokok',

                'rules' =>
                    'required|decimal|greater_than[0]',
            ],

            'persentase_bunga' => [
                'label' =>
                    'Persentase Bunga',

                'rules' =>
                    'required|decimal'
                    . '|greater_than_equal_to[0]'
                    . '|less_than_equal_to[100]',
            ],

            'keterangan' => [
                'label' =>
                    'Keterangan',

                'rules' =>
                    'permit_empty|max_length[1000]',
            ],

        ];
    }


    /**
     * ----------------------------------------------------------------------
     * Generate Nomor Piutang
     * ----------------------------------------------------------------------
     *
     * Format:
     *
     * PIU-00001
     * PIU-00002
     * PIU-00003
     */

    protected function generateNomorPiutang(): string
    {
        $last =
            $this->piutangModel
                ->withDeleted()
                ->orderBy(
                    'id',
                    'DESC'
                )
                ->first();

        $nextId =
            $last !== null
                ? ((int) $last['id'] + 1)
                : 1;

        return 'PIU-'
            . str_pad(
                (string) $nextId,
                5,
                '0',
                STR_PAD_LEFT
            );
    }
}