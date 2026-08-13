<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class AturanDendaVersiModel extends Model
{
    protected $table            = 'aturan_denda_versi';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

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

    protected $useTimestamps = true;

    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    /**
     * Status versi denda.
     */
    public const STATUS_DRAFT   = 'draft';
    public const STATUS_AKTIF   = 'aktif';
    public const STATUS_SELESAI = 'selesai';

    /**
     * Daftar status yang valid.
     *
     * @return array<int, string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_DRAFT,
            self::STATUS_AKTIF,
            self::STATUS_SELESAI,
        ];
    }

    /**
     * Validation rules.
     */
    protected $validationRules = [
        'kode_versi' => [
            'rules'  => 'required|max_length[50]',
            'errors' => [
                'required'   => 'Kode versi wajib diisi.',
                'max_length' => 'Kode versi maksimal 50 karakter.',
            ],
        ],

        'nama_versi' => [
            'rules'  => 'required|max_length[150]',
            'errors' => [
                'required'   => 'Nama versi wajib diisi.',
                'max_length' => 'Nama versi maksimal 150 karakter.',
            ],
        ],

        'tanggal_mulai' => [
            'rules'  => 'required|valid_date[Y-m-d]',
            'errors' => [
                'required'   => 'Tanggal mulai wajib diisi.',
                'valid_date' => 'Format tanggal mulai tidak valid.',
            ],
        ],

        'tanggal_selesai' => [
            'rules'  => 'permit_empty|valid_date[Y-m-d]',
            'errors' => [
                'valid_date' => 'Format tanggal selesai tidak valid.',
            ],
        ],

        'status' => [
            'rules'  => 'required|in_list[draft,aktif,selesai]',
            'errors' => [
                'required' => 'Status versi wajib diisi.',
                'in_list'  => 'Status versi tidak valid.',
            ],
        ],

        'keterangan' => [
            'rules'  => 'permit_empty|max_length[500]',
            'errors' => [
                'max_length' => 'Keterangan maksimal 500 karakter.',
            ],
        ],

        'created_by' => [
            'rules' => 'permit_empty|is_natural_no_zero',
        ],

        'updated_by' => [
            'rules' => 'permit_empty|is_natural_no_zero',
        ],

        'deleted_by' => [
            'rules' => 'permit_empty|is_natural_no_zero',
        ],
    ];

    protected $validationMessages = [];

    protected $skipValidation = false;

    /**
     * Ambil seluruh versi.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getAllVersions(): array
    {
        return $this
            ->orderBy('tanggal_mulai', 'DESC')
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    /**
     * Ambil versi berdasarkan ID.
     */
    public function findVersion(int $id): ?array
    {
        $result = $this->find($id);

        return $result ?: null;
    }

    /**
     * Ambil versi berdasarkan kode versi.
     */
    public function findByCode(string $kodeVersi): ?array
    {
        $result = $this
            ->where('kode_versi', $kodeVersi)
            ->first();

        return $result ?: null;
    }

    /**
     * Ambil versi yang sedang aktif berdasarkan tanggal.
     *
     * Status harus aktif dan tanggal hari ini berada dalam periode versi.
     */
    public function getActiveVersion(?string $tanggal = null): ?array
    {
        $tanggal ??= date('Y-m-d');

        $result = $this
            ->where('status', self::STATUS_AKTIF)
            ->where('tanggal_mulai <=', $tanggal)
            ->groupStart()
                ->where('tanggal_selesai >=', $tanggal)
                ->orWhere('tanggal_selesai IS NULL', null, false)
            ->groupEnd()
            ->orderBy('tanggal_mulai', 'DESC')
            ->first();

        return $result ?: null;
    }

    /**
     * Alias untuk mendapatkan versi yang berlaku.
     *
     * Versi draft tidak dianggap berlaku.
     */
    public function getApplicableVersion(?string $tanggal = null): ?array
    {
        return $this->getActiveVersion($tanggal);
    }

    /**
     * Cek apakah versi berstatus draft.
     */
    public function isDraft(array $versi): bool
    {
        return ($versi['status'] ?? null) === self::STATUS_DRAFT;
    }

    /**
     * Cek apakah versi berstatus aktif.
     */
    public function isActive(array $versi): bool
    {
        return ($versi['status'] ?? null) === self::STATUS_AKTIF;
    }

    /**
     * Cek apakah versi sudah selesai.
     */
    public function isFinished(array $versi): bool
    {
        return ($versi['status'] ?? null) === self::STATUS_SELESAI;
    }

    /**
     * Cek apakah periode versi valid.
     *
     * Jika tanggal selesai diisi, tanggal tersebut tidak boleh
     * lebih kecil dari tanggal mulai.
     */
    public function isValidPeriod(
        string $tanggalMulai,
        ?string $tanggalSelesai = null
    ): bool {
        if ($tanggalSelesai === null || $tanggalSelesai === '') {
            return true;
        }

        return $tanggalSelesai >= $tanggalMulai;
    }

    /**
     * Mencari versi AKTIF/SELESAI yang periodenya bertabrakan
     * dengan periode yang diberikan.
     *
     * Draft tidak dianggap sebagai penghalang.
     *
     * @return array<string, mixed>|null
     */
    public function hasPeriodOverlap(
        string $tanggalMulai,
        ?string $tanggalSelesai = null,
        ?int $exceptId = null
    ): ?array {
        $tanggalSelesai = $tanggalSelesai ?: '9999-12-31';

        $builder = $this
            ->whereIn('status', [
                self::STATUS_AKTIF,
                self::STATUS_SELESAI,
            ])
            ->where('tanggal_mulai <=', $tanggalSelesai)
            ->groupStart()
                ->where('tanggal_selesai >=', $tanggalMulai)
                ->orWhere('tanggal_selesai IS NULL', null, false)
            ->groupEnd();

        /*
        * Saat edit draft, versi yang sedang diedit
        * tidak boleh dianggap sebagai konflik.
        */
        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        $result = $builder
            ->orderBy('tanggal_mulai', 'ASC')
            ->first();

        return $result ?: null;
    }

    /**
     * Cek apakah kode versi sudah digunakan.
     */
    public function codeExists(
        string $kodeVersi,
        ?int $exceptId = null
    ): bool {
        $builder = $this
            ->where('kode_versi', $kodeVersi);

        if ($exceptId !== null) {
            $builder->where('id !=', $exceptId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Hitung jumlah versi berdasarkan status.
     */
    public function countByStatus(string $status): int
    {
        return $this
            ->where('status', $status)
            ->countAllResults();
    }

    /**
     * Ambil versi draft.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getDraftVersions(): array
    {
        return $this
            ->where('status', self::STATUS_DRAFT)
            ->orderBy('tanggal_mulai', 'ASC')
            ->findAll();
    }

    /**
     * Ambil versi aktif.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getActiveVersions(): array
    {
        return $this
            ->where('status', self::STATUS_AKTIF)
            ->orderBy('tanggal_mulai', 'DESC')
            ->findAll();
    }

    /**
     * Ambil versi selesai.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getFinishedVersions(): array
    {
        return $this
            ->where('status', self::STATUS_SELESAI)
            ->orderBy('tanggal_mulai', 'DESC')
            ->findAll();
    }
}