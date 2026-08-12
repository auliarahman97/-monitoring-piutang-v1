<?php

declare(strict_types=1);

namespace App\Models;

/**
 * --------------------------------------------------------------------------
 * Piutang Model
 * --------------------------------------------------------------------------
 *
 * Model transaksi piutang pada Sistem Monitoring Piutang.
 *
 * Tanggung jawab:
 *
 * - Data piutang customer
 * - Nominal pokok
 * - Bunga
 * - Referensi versi aturan denda
 * - Snapshot aturan denda
 * - Jatuh tempo
 * - Perhitungan denda
 * - Soft delete
 * - Audit trail
 *
 * Business logic pembayaran TIDAK dilakukan di model ini.
 * Proses pembayaran ditangani oleh PaymentService.
 */
class PiutangModel extends BaseModel
{
    /**
     * ----------------------------------------------------------------------
     * Table Configuration
     * ----------------------------------------------------------------------
     */

    protected $table = 'piutang';

    protected $primaryKey = 'id';

    protected $returnType = 'array';


    /**
     * ----------------------------------------------------------------------
     * Allowed Fields
     * ----------------------------------------------------------------------
     *
     * denda_versi_id:
     * Referensi ke versi aturan denda yang berlaku ketika
     * piutang dibuat.
     *
     * Snapshot:
     * Nilai aturan denda disalin ke piutang agar histori
     * tidak berubah ketika aturan baru dibuat.
     */

    protected $allowedFields = [
        'customer_id',

        'nomor_piutang',

        'tanggal_piutang',

        'tanggal_jatuh_tempo',

        'nominal_pokok',

        'persentase_bunga',

        'nominal_bunga',

        'denda_versi_id',

        /*
         * Snapshot aturan denda.
         */
        'persentase_denda',

        'periode_denda_hari',

        'maksimal_denda_persen',

        'keterangan',

        /*
         * Audit.
         */
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

        'customer_id' => [
            'label' => 'Customer',
            'rules' => [
                'required',
                'integer',
                'greater_than[0]',
            ],
        ],

        'nomor_piutang' => [
            'label' => 'Nomor Piutang',
            'rules' => [
                'required',
                'max_length[30]',
            ],
        ],

        'tanggal_piutang' => [
            'label' => 'Tanggal Piutang',
            'rules' => [
                'required',
                'valid_date[Y-m-d]',
            ],
        ],

        'tanggal_jatuh_tempo' => [
            'label' => 'Tanggal Jatuh Tempo',
            'rules' => [
                'required',
                'valid_date[Y-m-d]',
            ],
        ],

        'nominal_pokok' => [
            'label' => 'Nominal Pokok',
            'rules' => [
                'required',
                'decimal',
                'greater_than[0]',
            ],
        ],

        'persentase_bunga' => [
            'label' => 'Persentase Bunga',
            'rules' => [
                'required',
                'decimal',
                'greater_than_equal_to[0]',
                'less_than_equal_to[100]',
            ],
        ],

        'nominal_bunga' => [
            'label' => 'Nominal Bunga',
            'rules' => [
                'required',
                'decimal',
                'greater_than_equal_to[0]',
            ],
        ],

        'denda_versi_id' => [
            'label' => 'Versi Aturan Denda',
            'rules' => [
                'required',
                'integer',
                'greater_than[0]',
            ],
        ],

        'persentase_denda' => [
            'label' => 'Persentase Denda',
            'rules' => [
                'required',
                'decimal',
                'greater_than_equal_to[0]',
                'less_than_equal_to[100]',
            ],
        ],

        'periode_denda_hari' => [
            'label' => 'Periode Denda',
            'rules' => [
                'required',
                'integer',
                'greater_than[0]',
            ],
        ],

        'maksimal_denda_persen' => [
            'label' => 'Maksimal Denda',
            'rules' => [
                'required',
                'decimal',
                'greater_than_equal_to[0]',
                'less_than_equal_to[100]',
            ],
        ],

        'keterangan' => [
            'label' => 'Keterangan',
            'rules' => [
                'permit_empty',
                'max_length[1000]',
            ],
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Validation Messages
     * ----------------------------------------------------------------------
     */

    protected $validationMessages = [

        'customer_id' => [
            'required' =>
                'Customer wajib dipilih.',

            'integer' =>
                'Customer tidak valid.',

            'greater_than' =>
                'Customer tidak valid.',
        ],

        'nomor_piutang' => [
            'required' =>
                'Nomor piutang wajib diisi.',

            'max_length' =>
                'Nomor piutang maksimal 30 karakter.',
        ],

        'tanggal_piutang' => [
            'required' =>
                'Tanggal piutang wajib diisi.',

            'valid_date' =>
                'Tanggal piutang tidak valid.',
        ],

        'tanggal_jatuh_tempo' => [
            'required' =>
                'Tanggal jatuh tempo wajib diisi.',

            'valid_date' =>
                'Tanggal jatuh tempo tidak valid.',
        ],

        'nominal_pokok' => [
            'required' =>
                'Nominal pokok wajib diisi.',

            'decimal' =>
                'Nominal pokok harus berupa angka.',

            'greater_than' =>
                'Nominal pokok harus lebih dari 0.',
        ],

        'persentase_bunga' => [
            'required' =>
                'Persentase bunga wajib diisi.',

            'decimal' =>
                'Persentase bunga harus berupa angka.',

            'greater_than_equal_to' =>
                'Persentase bunga tidak boleh kurang dari 0%.',

            'less_than_equal_to' =>
                'Persentase bunga maksimal 100%.',
        ],

        'nominal_bunga' => [
            'required' =>
                'Nominal bunga wajib diisi.',

            'decimal' =>
                'Nominal bunga harus berupa angka.',

            'greater_than_equal_to' =>
                'Nominal bunga tidak boleh kurang dari 0.',
        ],

        'denda_versi_id' => [
            'required' =>
                'Versi aturan denda wajib ditentukan.',

            'integer' =>
                'Versi aturan denda tidak valid.',

            'greater_than' =>
                'Versi aturan denda tidak valid.',
        ],

        'persentase_denda' => [
            'required' =>
                'Persentase denda wajib diisi.',

            'decimal' =>
                'Persentase denda harus berupa angka.',

            'greater_than_equal_to' =>
                'Persentase denda tidak boleh kurang dari 0%.',

            'less_than_equal_to' =>
                'Persentase denda maksimal 100%.',
        ],

        'periode_denda_hari' => [
            'required' =>
                'Periode denda wajib diisi.',

            'integer' =>
                'Periode denda harus berupa bilangan bulat.',

            'greater_than' =>
                'Periode denda harus lebih dari 0 hari.',
        ],

        'maksimal_denda_persen' => [
            'required' =>
                'Maksimal denda wajib diisi.',

            'decimal' =>
                'Maksimal denda harus berupa angka.',

            'greater_than_equal_to' =>
                'Maksimal denda tidak boleh kurang dari 0%.',

            'less_than_equal_to' =>
                'Maksimal denda tidak boleh lebih dari 100%.',
        ],

        'keterangan' => [
            'max_length' =>
                'Keterangan maksimal 1000 karakter.',
        ],
    ];


    /**
     * ----------------------------------------------------------------------
     * Business Validation
     * ----------------------------------------------------------------------
     */

    /**
     * Memastikan tanggal jatuh tempo tidak lebih awal
     * daripada tanggal piutang.
     */
    public function validateDueDate(
        string $tanggalPiutang,
        string $tanggalJatuhTempo
    ): bool {
        try {
            $tanggalPiutangObj =
                new \DateTimeImmutable($tanggalPiutang);

            $tanggalJatuhTempoObj =
                new \DateTimeImmutable($tanggalJatuhTempo);

            return $tanggalJatuhTempoObj >= $tanggalPiutangObj;

        } catch (\Throwable) {
            return false;
        }
    }


    /**
     * Menghitung nominal bunga berdasarkan pokok
     * dan persentase bunga.
     */
    public function calculateInterest(
        float|int|string $nominalPokok,
        float|int|string $persentaseBunga
    ): float {
        $pokok =
            (float) $nominalPokok;

        $persentase =
            (float) $persentaseBunga;

        if ($pokok <= 0 || $persentase <= 0) {
            return 0.0;
        }

        return round(
            ($pokok * $persentase) / 100,
            2
        );
    }


    /**
     * Menghitung nominal denda untuk satu periode.
     *
     * Denda berdasarkan pokok awal.
     * Non-compounding.
     */
    public function calculatePenaltyPerPeriod(
        float|int|string $nominalPokok,
        float|int|string $persentaseDenda
    ): float {
        $pokok =
            (float) $nominalPokok;

        $persentase =
            (float) $persentaseDenda;

        if ($pokok <= 0 || $persentase <= 0) {
            return 0.0;
        }

        return round(
            ($pokok * $persentase) / 100,
            2
        );
    }


    /**
     * Menghitung jumlah periode keterlambatan.
     *
     * Ketentuan:
     *
     * - Tepat tanggal jatuh tempo = 0 periode.
     * - Setelah jatuh tempo = minimal 1 periode.
     * - Periode pertama langsung berlaku.
     * - Jumlah hari per periode mengikuti aturan.
     *
     * Contoh periode 30 hari:
     *
     * 1 hari  = 1 periode
     * 30 hari = 1 periode
     * 31 hari = 2 periode
     */
    public function calculatePenaltyPeriods(
        string $tanggalJatuhTempo,
        int $periodeHari,
        ?string $tanggalPerhitungan = null
    ): int {
        if ($periodeHari <= 0) {
            return 0;
        }

        $tanggalPerhitungan ??=
            date('Y-m-d');

        try {
            $jatuhTempo =
                new \DateTimeImmutable(
                    $tanggalJatuhTempo
                );

            $tanggalHitung =
                new \DateTimeImmutable(
                    $tanggalPerhitungan
                );

        } catch (\Throwable) {
            return 0;
        }

        if ($tanggalHitung <= $jatuhTempo) {
            return 0;
        }

        $selisihHari =
            (int) $jatuhTempo
                ->diff($tanggalHitung)
                ->days;

        if ($selisihHari <= 0) {
            return 0;
        }

        return (int) ceil(
            $selisihHari / $periodeHari
        );
    }


    /**
     * Menghitung total denda keterlambatan berdasarkan
     * snapshot aturan denda yang tersimpan pada piutang.
     *
     * Ketentuan:
     *
     * - Denda mulai setelah jatuh tempo.
     * - Tidak menggunakan grace period.
     * - Denda berdasarkan pokok awal.
     * - Non-compounding.
     * - Denda bertambah setiap periode.
     * - Memiliki batas maksimum akumulasi.
     */
    public function calculatePenalty(
        float $nominalPokok,
        string $tanggalJatuhTempo,
        string $tanggalPerhitungan,
        float $persentaseDenda,
        int $periodeHari,
        float $maksimalDendaPersen
    ): float {
        if ($nominalPokok <= 0) {
            return 0.0;
        }

        if (
            $persentaseDenda <= 0
            || $periodeHari <= 0
            || $maksimalDendaPersen <= 0
        ) {
            return 0.0;
        }

        try {
            $jatuhTempo =
                new \DateTimeImmutable(
                    $tanggalJatuhTempo
                );

            $tanggalHitung =
                new \DateTimeImmutable(
                    $tanggalPerhitungan
                );

        } catch (\Throwable) {
            return 0.0;
        }

        if ($tanggalHitung <= $jatuhTempo) {
            return 0.0;
        }

        $jumlahPeriode =
            $this->calculatePenaltyPeriods(
                $tanggalJatuhTempo,
                $periodeHari,
                $tanggalPerhitungan
            );

        if ($jumlahPeriode <= 0) {
            return 0.0;
        }

        $dendaPerPeriode =
            $this->calculatePenaltyPerPeriod(
                $nominalPokok,
                $persentaseDenda
            );

        $nominalDenda =
            $dendaPerPeriode
            * $jumlahPeriode;

        $maksimalDenda =
            $nominalPokok
            * ($maksimalDendaPersen / 100);

        return round(
            min(
                $nominalDenda,
                $maksimalDenda
            ),
            2
        );
    }


    /**
     * ----------------------------------------------------------------------
     * Query Piutang
     * ----------------------------------------------------------------------
     */

    /**
     * Mengambil seluruh piutang beserta:
     *
     * - Customer
     * - Versi aturan denda
     */
    public function getAllWithCustomer(): array
    {
        return $this
            ->select([
                'piutang.*',

                'customer.kode_customer',
                'customer.nama AS nama_customer',

                'aturan_denda_versi.kode_versi',
                'aturan_denda_versi.nama_versi',
            ])
            ->join(
                'customer',
                'customer.id = piutang.customer_id',
                'left'
            )
            ->join(
                'aturan_denda_versi',
                'aturan_denda_versi.id = piutang.denda_versi_id',
                'left'
            )
            ->orderBy(
                'piutang.id',
                'DESC'
            )
            ->findAll();
    }


    /**
     * Mengambil satu piutang berdasarkan ID.
     *
     * Tidak melakukan join.
     */
    public function getById(
        int $id
    ): ?array {
        return $this
            ->where(
                'piutang.id',
                $id
            )
            ->first();
    }


    /**
     * Mengambil satu piutang beserta:
     *
     * - Customer
     * - Versi aturan denda
     */
    public function getWithCustomer(
        int $id
    ): ?array {
        return $this
            ->select([
                'piutang.*',

                'customer.kode_customer',
                'customer.nama AS nama_customer',

                'aturan_denda_versi.kode_versi',
                'aturan_denda_versi.nama_versi',
                'aturan_denda_versi.tanggal_mulai AS versi_tanggal_mulai',
                'aturan_denda_versi.tanggal_selesai AS versi_tanggal_selesai',
            ])
            ->join(
                'customer',
                'customer.id = piutang.customer_id',
                'left'
            )
            ->join(
                'aturan_denda_versi',
                'aturan_denda_versi.id = piutang.denda_versi_id',
                'left'
            )
            ->where(
                'piutang.id',
                $id
            )
            ->first();
    }


    /**
     * Alias eksplisit untuk kebutuhan detail.
     */
    public function getDetail(
        int $id
    ): ?array {
        return $this->getWithCustomer($id);
    }
}