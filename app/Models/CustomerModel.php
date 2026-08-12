<?php

declare(strict_types=1);

namespace App\Models;

/**
 * --------------------------------------------------------------------------
 * Customer Model
 * --------------------------------------------------------------------------
 *
 * Model untuk master data customer pada Sistem Monitoring Piutang.
 *
 * Menangani:
 * - Master customer
 * - Validasi data customer
 * - Soft delete
 * - Audit trail
 * - Generator kode customer
 */
class CustomerModel extends BaseModel
{
    /**
     * ----------------------------------------------------------------------
     * Table Configuration
     * ----------------------------------------------------------------------
     */

    protected $table = 'customer';

    protected $primaryKey = 'id';

    protected $returnType = 'array';

    /**
     * ----------------------------------------------------------------------
     * Allowed Fields
     * ----------------------------------------------------------------------
     */

    protected $allowedFields = [
        'kode_customer',
        'nama',
        'nik',
        'alamat',
        'no_hp',
        'tanggal_terdaftar',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    /**
     * ----------------------------------------------------------------------
     * Validation Rules
     * ----------------------------------------------------------------------
     */

    protected $validationRules = [
        'nama' => [
            'label' => 'Nama Customer',
            'rules' => 'required|max_length[100]',
        ],

        'nik' => [
            'label' => 'NIK',
            'rules' => 'permit_empty|numeric|exact_length[16]',
        ],

        'alamat' => [
            'label' => 'Alamat',
            'rules' => 'permit_empty|max_length[255]',
        ],

        'no_hp' => [
            'label' => 'No. HP',
            'rules' => 'permit_empty|numeric|min_length[10]|max_length[15]',
        ],

        'tanggal_terdaftar' => [
            'label' => 'Tanggal Terdaftar',
            'rules' => 'required|valid_date[Y-m-d]',
        ],

        'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[0,1]',
        ],
    ];

    /**
     * ----------------------------------------------------------------------
     * Validation Messages
     * ----------------------------------------------------------------------
     */

    protected $validationMessages = [
        'nama' => [
            'required'   => 'Nama customer wajib diisi.',
            'max_length' => 'Nama customer maksimal 100 karakter.',
        ],

        'nik' => [
            'numeric'      => 'NIK hanya boleh berisi angka.',
            'exact_length' => 'NIK harus terdiri dari 16 digit.',
        ],

        'alamat' => [
            'max_length' => 'Alamat maksimal 255 karakter.',
        ],

        'no_hp' => [
            'numeric'      => 'No. HP hanya boleh berisi angka.',
            'min_length'  => 'No. HP minimal 10 digit.',
            'max_length' => 'No. HP maksimal 15 digit.',
        ],

        'tanggal_terdaftar' => [
            'required'   => 'Tanggal terdaftar wajib diisi.',
            'valid_date' => 'Tanggal terdaftar tidak valid.',
        ],

        'status' => [
            'required' => 'Status wajib dipilih.',
            'in_list'  => 'Status tidak valid.',
        ],
    ];

    /**
     * ----------------------------------------------------------------------
     * Customer Code
     * ----------------------------------------------------------------------
     */

    /**
     * Membuat kode customer berdasarkan ID database.
     *
     * Format:
     * CUST-00001
     * CUST-00002
     * CUST-00003
     */
    public function generateKodeCustomer(int $id): string
    {
        return 'CUST-' . str_pad(
            (string) $id,
            5,
            '0',
            STR_PAD_LEFT
        );
    }
}