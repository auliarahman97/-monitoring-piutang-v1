<?php

declare(strict_types=1);

namespace App\Models;

/**
 * --------------------------------------------------------------------------
 * Aturan Denda Versi Model
 * --------------------------------------------------------------------------
 *
 * Menangani:
 * - Versioning aturan denda
 * - Periode berlaku
 * - Status draft / aktif / selesai
 * - Validasi periode
 * - Validasi overlap
 * - Soft delete
 * - Audit trail
 *
 * Business Rule:
 *
 * 1. Draft
 *    - Fleksibel untuk diedit.
 *    - Periode tidak boleh overlap dengan aktif/selesai.
 *
 * 2. Aktif
 *    - Tidak boleh diubah periodenya.
 *    - Tidak boleh diedit.
 *
 * 3. Selesai
 *    - Immutable.
 *    - Tidak boleh diedit.
 *
 * 4. Draft tidak pernah menjadi applicable version.
 */
class AturanDendaVersiModel extends BaseModel
{
    /**
     * ----------------------------------------------------------------------
     * Table Configuration
     * ----------------------------------------------------------------------
     */

    protected $table = 'aturan_denda_versi';

    protected $primaryKey = 'id';

    protected $returnType = 'array';


    /**
     * ----------------------------------------------------------------------
     * Allowed Fields
     * ----------------------------------------------------------------------
     */

    protected $allowedFields = [
        'kode_versi',
        'nama_versi',
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
        'kode_versi' => [
            'label' => 'Kode Versi',
            'rules' => 'required|max_length[30]',
        ],

        'nama_versi' => [
            'label' => 'Nama Versi',
            'rules' => 'required|max_length[150]',
        ],

        'tanggal_mulai' => [
            'label' => 'Tanggal Mulai',
            'rules' => 'required|valid_date[Y-m-d]',
        ],

        'tanggal_selesai' => [
            'label' => 'Tanggal Selesai',
            'rules' => 'permit_empty|valid_date[Y-m-d]',
        ],

        'status' => [
            'label' => 'Status',
            'rules' => 'required|in_list[draft,aktif,selesai]',
        ],

        'keterangan' => [
            'label' => 'Keterangan',
            'rules' => 'permit_empty',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Status Constants
     * ----------------------------------------------------------------------
     */

    public const STATUS_DRAFT = 'draft';

    public const STATUS_AKTIF = 'aktif';

    public const STATUS_SELESAI = 'selesai';


    /**
     * ----------------------------------------------------------------------
     * Ambil Semua Versi
     * ----------------------------------------------------------------------
     */

    public function getAllVersions(): array
    {
        return $this
            ->orderBy(
                'tanggal_mulai',
                'DESC'
            )
            ->findAll();
    }


    /**
     * ----------------------------------------------------------------------
     * Ambil Versi Berdasarkan ID
     * ----------------------------------------------------------------------
     */

    public function getById(
        int $id
    ): ?array {

        return $this
            ->where(
                'id',
                $id
            )
            ->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Ambil Versi yang Berlaku
     * ----------------------------------------------------------------------
     *
     * Draft TIDAK boleh ikut dalam pencarian.
     *
     * Yang boleh menjadi applicable:
     * - aktif
     * - selesai
     */

    public function getApplicableVersion(
        string $tanggal
    ): ?array {

        return $this
            ->whereIn(
                'status',
                [
                    self::STATUS_AKTIF,
                    self::STATUS_SELESAI,
                ]
            )
            ->where(
                'tanggal_mulai <=',
                $tanggal
            )
            ->groupStart()
                ->where(
                    'tanggal_selesai >=',
                    $tanggal
                )
                ->orWhere(
                    'tanggal_selesai IS NULL',
                    null,
                    false
                )
            ->groupEnd()
            ->orderBy(
                'tanggal_mulai',
                'DESC'
            )
            ->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Ambil Versi Aktif
     * ----------------------------------------------------------------------
     */

    public function getActiveVersion(): ?array
    {
        return $this
            ->where(
                'status',
                self::STATUS_AKTIF
            )
            ->orderBy(
                'tanggal_mulai',
                'DESC'
            )
            ->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Validasi Periode
     * ----------------------------------------------------------------------
     */

    public function validatePeriod(
        string $tanggalMulai,
        ?string $tanggalSelesai = null
    ): ?string {

        if (
            $tanggalSelesai !== null
            && $tanggalSelesai < $tanggalMulai
        ) {

            return
                'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        }

        return null;
    }


    /**
     * ----------------------------------------------------------------------
     * Cek Tanggal Dalam Versi
     * ----------------------------------------------------------------------
     */

    public function isDateWithinVersion(
        array $versi,
        string $tanggal
    ): bool {

        if (
            $tanggal < $versi['tanggal_mulai']
        ) {

            return false;
        }


        if (
            ! empty($versi['tanggal_selesai'])
            && $tanggal > $versi['tanggal_selesai']
        ) {

            return false;
        }


        return true;
    }


    /**
     * ----------------------------------------------------------------------
     * Cek Apakah Versi Immutable
     * ----------------------------------------------------------------------
     *
     * Aktif dan selesai tidak boleh diedit.
     */

    public function isImmutable(
        array $versi
    ): bool {

        return in_array(
            $versi['status'] ?? null,
            [
                self::STATUS_AKTIF,
                self::STATUS_SELESAI,
            ],
            true
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Cek Apakah Versi Draft
     * ----------------------------------------------------------------------
     */

    public function isDraft(
        array $versi
    ): bool {

        return (
            ($versi['status'] ?? null)
            === self::STATUS_DRAFT
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Cek Overlap Dengan Versi Aktif / Selesai
     * ----------------------------------------------------------------------
     *
     * Draft boleh fleksibel, tetapi tidak boleh overlap
     * dengan versi yang sudah aktif atau selesai.
     *
     * Draft lain tidak menjadi penghalang.
     */

    public function hasPeriodOverlap(
        string $tanggalMulai,
        ?string $tanggalSelesai = null,
        ?int $exceptId = null
    ): bool {

        /*
         * Periode tanpa tanggal selesai dianggap tidak terbatas.
         */

        $tanggalSelesaiUntukQuery =
            $tanggalSelesai ?? '9999-12-31';


        $builder =
            $this
                ->whereIn(
                    'status',
                    [
                        self::STATUS_AKTIF,
                        self::STATUS_SELESAI,
                    ]
                )
                ->where(
                    'tanggal_mulai <=',
                    $tanggalSelesaiUntukQuery
                )
                ->groupStart()
                    ->where(
                        'tanggal_selesai >=',
                        $tanggalMulai
                    )
                    ->orWhere(
                        'tanggal_selesai IS NULL',
                        null,
                        false
                    )
                ->groupEnd();


        /*
         * Saat edit draft, jangan membandingkan
         * dengan dirinya sendiri.
         */

        if ($exceptId !== null) {

            $builder->where(
                'id !=',
                $exceptId
            );
        }


        return (
            $builder->countAllResults() > 0
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Cari Versi Yang Bentrok
     * ----------------------------------------------------------------------
     *
     * Berguna untuk memberikan pesan error yang lebih informatif.
     */

    public function findOverlappingVersion(
        string $tanggalMulai,
        ?string $tanggalSelesai = null,
        ?int $exceptId = null
    ): ?array {

        $tanggalSelesaiUntukQuery =
            $tanggalSelesai ?? '9999-12-31';


        $builder =
            $this
                ->whereIn(
                    'status',
                    [
                        self::STATUS_AKTIF,
                        self::STATUS_SELESAI,
                    ]
                )
                ->where(
                    'tanggal_mulai <=',
                    $tanggalSelesaiUntukQuery
                )
                ->groupStart()
                    ->where(
                        'tanggal_selesai >=',
                        $tanggalMulai
                    )
                    ->orWhere(
                        'tanggal_selesai IS NULL',
                        null,
                        false
                    )
                ->groupEnd();


        if ($exceptId !== null) {

            $builder->where(
                'id !=',
                $exceptId
            );
        }


        return $builder->first();
    }


    /**
     * ----------------------------------------------------------------------
     * Deprecated: closePreviousVersion()
     * ----------------------------------------------------------------------
     *
     * Method ini sengaja tidak digunakan lagi.
     *
     * Business rule baru:
     *
     * - Aktif tidak boleh diubah.
     * - Selesai tidak boleh diubah.
     *
     * Karena itu sistem TIDAK BOLEH otomatis mengubah
     * tanggal_selesai versi sebelumnya.
     */

    public function closePreviousVersion(
        string $tanggalMulaiBaru,
        int $newVersionId
    ): bool {

        /*
         * Tidak melakukan apa-apa.
         *
         * Dipertahankan hanya untuk mencegah
         * pemanggilan lama menyebabkan perubahan
         * terhadap data historis.
         */

        return true;
    }
}