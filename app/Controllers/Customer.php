<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Models\CustomerModel;
use CodeIgniter\HTTP\RedirectResponse;
use App\Services\PiutangMonitoringService;

class Customer extends BaseController
{
    /**
     * Model customer.
     */
    protected CustomerModel $customerModel;

    protected PiutangMonitoringService $piutangMonitoringService;

    public function __construct()
    {
        $this->customerModel =
            new CustomerModel();

        $this->piutangMonitoringService =
            new PiutangMonitoringService();
    }

    /**
     * ----------------------------------------------------------------------
     * Index
     * ----------------------------------------------------------------------
     */

    /**
     * Menampilkan seluruh customer,
     * termasuk customer yang sudah tidak aktif.
     */
    public function index(): string
    {
        $data = [
            'title' =>
                'Customer',

            'customer' =>
                $this->customerModel
                    ->withDeleted()
                    ->orderBy(
                        'id',
                        'DESC'
                    )
                    ->findAll(),
        ];

        return view(
            'customer/index',
            $data
        );
    }

    /**
     * ----------------------------------------------------------------------
     * Create
     * ----------------------------------------------------------------------
     */

    /**
     * Menampilkan form tambah customer.
     */
    public function create(): string
    {
        return view('customer/create', [
            'title' => 'Tambah Customer',
        ]);
    }

    /**
     * ----------------------------------------------------------------------
     * Store
     * ----------------------------------------------------------------------
     */

    /**
     * Menyimpan customer baru.
     */
    public function store(): RedirectResponse
    {
        if (! $this->validateModel($this->customerModel)) {
            return $this->backWithInput();
        }

        $data = $this->getFormData();

        $data['created_by'] = $this->currentUserId();

        /*
         * Kode customer harus diisi sejak INSERT karena
         * kolom kode_customer di database bersifat NOT NULL.
         *
         * Kita gunakan kode sementara yang unik.
         * Setelah ID database diperoleh, kode akan diganti
         * menjadi CUST-00001, CUST-00002, dan seterusnya.
         */
        $data['kode_customer'] = 'CUST-TMP-' . bin2hex(random_bytes(4));

        $db = db_connect();

        $db->transStart();

        $id = $this->customerModel->insert($data, true);

        if ($id === false) {
            $db->transRollback();

            return $this->backWithInput();
        }

        /*
         * Generate kode customer berdasarkan ID database.
         *
         * Contoh:
         * ID 1  -> CUST-00001
         * ID 25 -> CUST-00025
         */
        $kodeCustomer = $this->customerModel
            ->generateKodeCustomer((int) $id);

        $updated = $this->customerModel->update($id, [
            'kode_customer' => $kodeCustomer,
        ]);

        if (! $updated) {
            $db->transRollback();

            return $this->backWithInput();
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->redirectError(
                'customer',
                'Data customer gagal disimpan.'
            );
        }

        return $this->redirectSuccess(
            'customer',
            'Data customer berhasil ditambahkan.'
        );
    }

    /**
     * ----------------------------------------------------------------------
     * Edit
     * ----------------------------------------------------------------------
     */

    /**
     * Menampilkan form edit customer.
     */
    public function edit(int $id): RedirectResponse|string
    {
        $customer = $this->customerModel->find($id);

        if ($customer === null) {
            return $this->redirectError(
                'customer',
                'Data customer tidak ditemukan.'
            );
        }

        return view('customer/edit', [
            'title'    => 'Edit Customer',
            'customer' => $customer,
        ]);
    }

    /**
     * ----------------------------------------------------------------------
     * Update
     * ----------------------------------------------------------------------
     */

    /**
     * Memperbarui data customer.
     */
    public function update(int $id): RedirectResponse
    {
        $customer = $this->customerModel->find($id);

        if ($customer === null) {
            return $this->redirectError(
                'customer',
                'Data customer tidak ditemukan.'
            );
        }

        if (! $this->validateModel($this->customerModel)) {
            return $this->backWithInput();
        }

        $data = $this->getFormData();

        /*
         * Kode customer sengaja tidak dimasukkan ke data update.
         *
         * Kode customer merupakan identitas sistem dan tidak boleh
         * diubah oleh user.
         */
        $data['updated_by'] = $this->currentUserId();

        if (! $this->customerModel->update($id, $data)) {
            return $this->backWithInput();
        }

        return $this->redirectSuccess(
            'customer',
            'Data customer berhasil diperbarui.'
        );
    }

    /**
     * ----------------------------------------------------------------------
     * Delete / Nonaktifkan
     * ----------------------------------------------------------------------
     *
     * Customer hanya boleh dinonaktifkan apabila:
     *
     * - belum pernah memiliki piutang, atau
     * - seluruh piutang sudah lunas.
     *
     * Customer yang masih memiliki sisa tagihan tidak boleh dinonaktifkan.
     *
     * Proses menggunakan soft delete sehingga histori transaksi
     * tetap tersimpan.
     */
    public function delete(int $id): RedirectResponse
    {
        $customer =
            $this->customerModel
                ->find($id);

        if ($customer === null) {

            return $this->redirectError(
                'customer',
                'Data customer tidak ditemukan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Cek piutang yang masih outstanding
        |--------------------------------------------------------------------------
        */

        $hasOutstanding =
            $this->piutangMonitoringService
                ->hasOutstandingByCustomer(
                    $id
                );

        if ($hasOutstanding) {

            return $this->redirectError(
                'customer',
                'Customer tidak dapat dinonaktifkan karena masih memiliki piutang yang belum lunas.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Soft delete
        |--------------------------------------------------------------------------
        */

        $db =
            db_connect();

        $db->transStart();


        /*
        |--------------------------------------------------------------------------
        | Catat audit + status
        |--------------------------------------------------------------------------
        */

        $updated =
            $this->customerModel
                ->update(
                    $id,
                    [
                        'status' =>
                            0,

                        'deleted_by' =>
                            $this->currentUserId(),
                    ]
                );

        if (! $updated) {

            $db->transRollback();

            return $this->redirectError(
                'customer',
                'Customer gagal dinonaktifkan.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Soft delete
        |--------------------------------------------------------------------------
        */

        $deleted =
            $this->customerModel
                ->delete($id);

        if (! $deleted) {

            $db->transRollback();

            return $this->redirectError(
                'customer',
                'Customer gagal dinonaktifkan.'
            );
        }


        $db->transComplete();


        if (! $db->transStatus()) {

            return $this->redirectError(
                'customer',
                'Customer gagal dinonaktifkan.'
            );
        }


        return $this->redirectSuccess(
            'customer',
            'Customer berhasil dinonaktifkan.'
        );
    }

    /**
     * ----------------------------------------------------------------------
     * Form Data
     * ----------------------------------------------------------------------
     */

    /**
     * Mengambil data form yang diizinkan.
     *
     * Kode customer tidak diambil dari form karena
     * dibuat otomatis oleh sistem.
     *
     * @return array<string, mixed>
     */
    protected function getFormData(): array
    {
        return [
            'nama'              => trim(
                (string) $this->request->getPost('nama')
            ),

            'nik'               => trim(
                (string) $this->request->getPost('nik')
            ),

            'no_hp'             => trim(
                (string) $this->request->getPost('no_hp')
            ),

            'alamat'            => trim(
                (string) $this->request->getPost('alamat')
            ),

            'tanggal_terdaftar' => $this->request
                ->getPost('tanggal_terdaftar'),

            'status'            => $this->request
                ->getPost('status'),
        ];
    }
}