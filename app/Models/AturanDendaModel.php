<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AturanDendaModel extends Model
{
    protected $table            = 'aturan_denda';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'versi_id',
        'nama_aturan',
        'min_nominal',
        'max_nominal',
        'persentase_denda',
        'periode_hari',
        'maksimal_denda_persen',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $useTimestamps = true;

    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Validation rules.
     */
    protected $validationRules = [
        'versi_id' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'Versi denda wajib dipilih.',
                'is_natural_no_zero' => 'Versi denda tidak valid.',
            ],
        ],

        'nama_aturan' => [
            'rules'  => 'required|max_length[150]',
            'errors' => [
                'required'  => 'Nama aturan denda wajib diisi.',
                'max_length' => 'Nama aturan denda maksimal 150 karakter.',
            ],
        ],

        'min_nominal' => [
            'rules'  => 'required|decimal',
            'errors' => [
                'required' => 'Nominal minimum wajib diisi.',
                'decimal'  => 'Nominal minimum harus berupa angka.',
            ],
        ],

        'max_nominal' => [
            'rules'  => 'permit_empty|decimal',
            'errors' => [
                'decimal' => 'Nominal maksimum harus berupa angka.',
            ],
        ],

        'persentase_denda' => [
            'rules'  => 'required|decimal',
            'errors' => [
                'required' => 'Persentase denda wajib diisi.',
                'decimal'  => 'Persentase denda harus berupa angka.',
            ],
        ],

        'periode_hari' => [
            'rules'  => 'required|is_natural_no_zero',
            'errors' => [
                'required'           => 'Periode denda wajib diisi.',
                'is_natural_no_zero' => 'Periode denda harus berupa bilangan positif.',
            ],
        ],

        'maksimal_denda_persen' => [
            'rules'  => 'required|decimal',
            'errors' => [
                'required' => 'Maksimal denda wajib diisi.',
                'decimal'  => 'Maksimal denda harus berupa angka.',
            ],
        ],

        'keterangan' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'Keterangan maksimal 500 karakter.',
            ],
        ],

        'created_by' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
        ],

        'updated_by' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
        ],

        'deleted_by' => [
            'rules'  => 'permit_empty|is_natural_no_zero',
        ],
    ];

    protected $validationMessages = [];

    protected $skipValidation = false;

    /**
     * Ambil seluruh aturan berdasarkan versi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByVersionId(int $versiId): array
    {
        return $this
            ->where('versi_id', $versiId)
            ->orderBy('min_nominal', 'ASC')
            ->findAll();
    }

    /**
     * Ambil satu aturan berdasarkan ID dan versi.
     *
     * Pembatasan versi membantu mencegah aturan dari versi lain
     * ikut terbaca pada proses edit/detail.
     */
    public function findByVersion(int $id, int $versiId): ?array
    {
        $result = $this
            ->where('id', $id)
            ->where('versi_id', $versiId)
            ->first();

        return $result ?: null;
    }

    /**
     * Hitung jumlah aturan aktif pada suatu versi.
     */
    public function countByVersionId(int $versiId): int
    {
        return $this
            ->where('versi_id', $versiId)
            ->countAllResults();
    }

    /**
     * Hapus seluruh aturan milik suatu versi.
     *
     * Menggunakan soft delete karena model mengaktifkan useSoftDeletes.
     */
    public function deleteByVersionId(int $versiId): bool
    {
        return $this
            ->where('versi_id', $versiId)
            ->delete();
    }

    /**
     * Cek apakah terdapat aturan lain yang menggunakan rentang nominal
     * yang sama dalam versi tertentu.
     */
    public function existsRange(
        int $versiId,
        float $minNominal,
        ?float $maxNominal,
        ?int $exceptId = null
    ): bool {
        $builder = $this
            ->where('versi_id', $versiId)
            ->where('min_nominal', $minNominal);

        if ($maxNominal === null) {
            $builder->where('max_nominal IS NULL', null, false);
        } else {
            $builder->where('max_nominal', $maxNominal);
        }

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }
}