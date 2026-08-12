<?php

declare(strict_types=1);

namespace App\Models;

/**
 * --------------------------------------------------------------------------
 * Aturan Denda Model
 * --------------------------------------------------------------------------
 *
 * Model detail aturan denda pada Sistem Monitoring Piutang.
 *
 * Struktur:
 *
 * AturanDendaVersi
 *      │
 *      └── AturanDenda
 *
 * Model ini menangani:
 * - Detail/rentang nominal dalam sebuah versi aturan
 * - Persentase denda
 * - Periode denda
 * - Batas maksimum denda
 * - Validasi rentang nominal
 * - Validasi overlap dalam satu versi
 * - Pencarian rule berdasarkan versi + nominal
 * - Soft delete
 * - Audit trail
 */
class AturanDendaModel extends BaseModel
{
    /**
     * ----------------------------------------------------------------------
     * Table Configuration
     * ----------------------------------------------------------------------
     */

    protected $table      = 'aturan_denda';

    protected $primaryKey = 'id';

    protected $returnType = 'array';


    /**
     * ----------------------------------------------------------------------
     * Allowed Fields
     * ----------------------------------------------------------------------
     */

    protected $allowedFields = [
        'versi_id',

        'nama_aturan',

        'min_nominal',
        'max_nominal',

        'persentase_denda',

        'periode_hari',

        'maksimal_denda_persen',

        /*
         * Field legacy.
         *
         * Masih dipertahankan karena kolomnya masih ada di database.
         * Namun secara arsitektur baru, tanggal dan status authoritative
         * berada pada aturan_denda_versi.
         */
        'tanggal_mulai',
        'tanggal_selesai',
        'status',

        'keterangan',

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
        'versi_id' => [
            'label' => 'Versi Aturan Denda',
            'rules' => 'required|integer|greater_than[0]',
        ],

        'nama_aturan' => [
            'label' => 'Nama Aturan',
            'rules' => 'required|max_length[100]',
        ],

        'min_nominal' => [
            'label' => 'Minimal Nominal',
            'rules' => 'required|decimal|greater_than[0]',
        ],

        'max_nominal' => [
            'label' => 'Maksimal Nominal',
            'rules' => 'permit_empty|decimal|greater_than[0]',
        ],

        'persentase_denda' => [
            'label' => 'Persentase Denda',
            'rules' => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        ],

        'periode_hari' => [
            'label' => 'Periode Denda',
            'rules' => 'required|integer|greater_than[0]',
        ],

        'maksimal_denda_persen' => [
            'label' => 'Maksimal Denda',
            'rules' => 'required|decimal|greater_than[0]|less_than_equal_to[100]',
        ],

        /*
         * Field legacy dibuat optional.
         *
         * Tanggal/status sekarang berasal dari versi.
         */
        'tanggal_mulai' => [
            'label' => 'Tanggal Mulai',
            'rules' => 'permit_empty|valid_date[Y-m-d]',
        ],

        'tanggal_selesai' => [
            'label' => 'Tanggal Selesai',
            'rules' => 'permit_empty|valid_date[Y-m-d]',
        ],

        'status' => [
            'label' => 'Status',
            'rules' => 'permit_empty|in_list[0,1]',
        ],

        'keterangan' => [
            'label' => 'Keterangan',
            'rules' => 'permit_empty',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Validation Messages
     * ----------------------------------------------------------------------
     */

    protected $validationMessages = [
        'versi_id' => [
            'required'     => 'Versi aturan denda wajib dipilih.',
            'integer'      => 'Versi aturan denda tidak valid.',
            'greater_than' => 'Versi aturan denda tidak valid.',
        ],

        'nama_aturan' => [
            'required'   => 'Nama aturan wajib diisi.',
            'max_length' => 'Nama aturan maksimal 100 karakter.',
        ],

        'min_nominal' => [
            'required'     => 'Minimal nominal wajib diisi.',
            'decimal'      => 'Minimal nominal harus berupa angka.',
            'greater_than' => 'Minimal nominal harus lebih dari 0.',
        ],

        'max_nominal' => [
            'decimal'      => 'Maksimal nominal harus berupa angka.',
            'greater_than' => 'Maksimal nominal harus lebih dari 0.',
        ],

        'persentase_denda' => [
            'required'           => 'Persentase denda wajib diisi.',
            'decimal'            => 'Persentase denda harus berupa angka.',
            'greater_than'       => 'Persentase denda harus lebih dari 0%.',
            'less_than_equal_to' => 'Persentase denda maksimal 100%.',
        ],

        'periode_hari' => [
            'required'     => 'Periode denda wajib diisi.',
            'integer'      => 'Periode denda harus berupa bilangan bulat.',
            'greater_than' => 'Periode denda harus lebih dari 0 hari.',
        ],

        'maksimal_denda_persen' => [
            'required'           => 'Batas maksimal denda wajib diisi.',
            'decimal'            => 'Batas maksimal denda harus berupa angka.',
            'greater_than'       => 'Batas maksimal denda harus lebih dari 0%.',
            'less_than_equal_to' => 'Batas maksimal denda tidak boleh lebih dari 100%.',
        ],

        'tanggal_mulai' => [
            'valid_date' => 'Tanggal mulai tidak valid.',
        ],

        'tanggal_selesai' => [
            'valid_date' => 'Tanggal selesai tidak valid.',
        ],

        'status' => [
            'in_list' => 'Status tidak valid.',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Business Validation
     * ----------------------------------------------------------------------
     */

    /**
     * Memvalidasi hubungan antara minimal dan maksimal nominal.
     *
     * max_nominal boleh NULL yang berarti tidak memiliki batas atas.
     */
    public function validateNominalRange(
        float|int|string $minNominal,
        float|int|string|null $maxNominal
    ): bool {
        $min = (float) $minNominal;

        if ($min <= 0) {
            return false;
        }

        if ($maxNominal === null || $maxNominal === '') {
            return true;
        }

        return (float) $maxNominal > $min;
    }


    /**
     * Mengambil aturan berdasarkan ID.
     */
    public function getById(int $id): ?array
    {
        return $this
            ->where('id', $id)
            ->first();
    }


    /**
     * Mengambil seluruh aturan dalam satu versi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getByVersion(int $versiId): array
    {
        return $this
            ->where('versi_id', $versiId)
            ->orderBy('min_nominal', 'ASC')
            ->orderBy('id', 'ASC')
            ->findAll();
    }


    /**
     * Mengambil satu aturan yang sesuai dengan nominal
     * dalam versi tertentu.
     *
     * @param int                $versiId
     * @param float|int|string   $nominalPokok
     *
     * @return array<string, mixed>|null
     */
    public function getApplicableRule(
        int $versiId,
        float|int|string $nominalPokok
    ): ?array {
        $nominal = (float) $nominalPokok;

        if ($nominal <= 0) {
            return null;
        }

        return $this
            ->where('versi_id', $versiId)
            ->where('min_nominal <=', $nominal)
            ->groupStart()
                ->where('max_nominal >=', $nominal)
                ->orWhere('max_nominal IS NULL', null, false)
            ->groupEnd()
            ->orderBy('min_nominal', 'DESC')
            ->orderBy('id', 'DESC')
            ->first();
    }


    /**
     * Mengecek apakah rentang nominal bertabrakan
     * dengan aturan lain DALAM VERSI YANG SAMA.
     *
     * @param int                $versiId
     * @param float|int|string   $minNominal
     * @param float|int|string|null $maxNominal
     * @param int|null           $excludeId
     */
    public function hasOverlappingRange(
        int $versiId,
        float|int|string $minNominal,
        float|int|string|null $maxNominal = null,
        ?int $excludeId = null
    ): bool {
        $builder = $this->builder();

        $builder
            ->where('versi_id', $versiId);


        /*
         * Range baru tanpa batas atas.
         *
         * Contoh:
         *
         * > Rp50 juta
         *
         * Akan overlap dengan existing yang:
         * - min <= min baru
         * - max >= min baru
         * - atau max NULL
         */
        if ($maxNominal === null || $maxNominal === '') {

            $builder
                ->where('min_nominal <=', $minNominal)
                ->groupStart()
                    ->where('max_nominal >=', $minNominal)
                    ->orWhere(
                        'max_nominal IS NULL',
                        null,
                        false
                    )
                ->groupEnd();

        } else {

            /*
             * Range baru dengan batas atas.
             *
             * A <= D
             * dan
             * (B >= C atau B NULL)
             */
            $builder
                ->where('min_nominal <=', $maxNominal)
                ->groupStart()
                    ->where('max_nominal >=', $minNominal)
                    ->orWhere(
                        'max_nominal IS NULL',
                        null,
                        false
                    )
                ->groupEnd();
        }


        /*
         * Saat update detail, dirinya sendiri
         * tidak boleh dianggap overlap.
         */
        if ($excludeId !== null) {
            $builder->where('id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }


    /**
     * Memvalidasi apakah sebuah nominal memiliki rule
     * dalam versi tertentu.
     */
    public function hasRuleForNominal(
        int $versiId,
        float|int|string $nominalPokok
    ): bool {
        return $this->getApplicableRule(
            $versiId,
            $nominalPokok
        ) !== null;
    }


    /**
     * ----------------------------------------------------------------------
     * Helper Business Validation
     * ----------------------------------------------------------------------
     */

    /**
     * Mengembalikan pesan error untuk rentang nominal.
     */
    public function getNominalRangeError(
        float|int|string $minNominal,
        float|int|string|null $maxNominal
    ): ?string {
        if (! $this->validateNominalRange(
            $minNominal,
            $maxNominal
        )) {
            return 'Maksimal nominal harus lebih besar dari minimal nominal.';
        }

        return null;
    }


    /**
     * Mengembalikan pesan error jika rentang
     * bertabrakan dalam versi tertentu.
     */
    public function getOverlapError(
        int $versiId,
        float|int|string $minNominal,
        float|int|string|null $maxNominal = null,
        ?int $excludeId = null
    ): ?string {
        if ($this->hasOverlappingRange(
            $versiId,
            $minNominal,
            $maxNominal,
            $excludeId
        )) {
            return 'Rentang nominal bertabrakan dengan aturan denda pada versi ini.';
        }

        return null;
    }

    /**
     * Mengambil seluruh rentang dalam satu versi.
     */
    public function getByVersionId(
        int $versiId
    ): array {

        return $this
            ->where('versi_id', $versiId)
            ->orderBy(
                'min_nominal',
                'ASC'
            )
            ->findAll();
    }

}