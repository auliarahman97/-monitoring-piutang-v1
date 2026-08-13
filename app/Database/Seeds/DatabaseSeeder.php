<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Models\AturanDendaModel;
use App\Models\AturanDendaVersiModel;
use App\Models\CustomerModel;
use App\Models\PiutangModel;
use App\Services\PaymentService;
use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use RuntimeException;
use Throwable;

class DatabaseSeeder extends Seeder
{
    /**
     * ================================================================
     * KONFIGURASI USER DEMO
     * ================================================================
     */
    private array $users = [
        [
            'username' => 'admin.demo',
            'email'    => 'admin.demo@monitoring-piutang.test',
            'password' => 'DemoAdmin123!',
            'group'    => 'admin',
        ],
        [
            'username' => 'admin.demo2',
            'email'    => 'admin.demo2@monitoring-piutang.test',
            'password' => 'DemoAdmin123!',
            'group'    => 'admin',
        ],
        [
            'username' => 'petugas.demo',
            'email'    => 'petugas.demo@monitoring-piutang.test',
            'password' => 'DemoPetugas123!',
            'group'    => 'petugas',
        ],
        [
            'username' => 'petugas.demo2',
            'email'    => 'petugas.demo2@monitoring-piutang.test',
            'password' => 'DemoPetugas123!',
            'group'    => 'petugas',
        ],
        [
            'username' => 'pimpinan.demo',
            'email'    => 'pimpinan.demo@monitoring-piutang.test',
            'password' => 'DemoPimpinan123!',
            'group'    => 'pimpinan',
        ],
        [
            'username' => 'pimpinan.demo2',
            'email'    => 'pimpinan.demo2@monitoring-piutang.test',
            'password' => 'DemoPimpinan123!',
            'group'    => 'pimpinan',
        ],
    ];

    /**
     * User yang digunakan sebagai pembuat data demo.
     */
    private int $adminUserId = 0;

    /**
     * Jalankan seluruh demo database.
     */
    public function run(): void
    {
        $db = db_connect();

        echo PHP_EOL;
        echo "================================================" . PHP_EOL;
        echo " MONITORING PIUTANG - DATABASE SEEDER" . PHP_EOL;
        echo "================================================" . PHP_EOL;

        try {
            /*
             * ------------------------------------------------------------
             * 1. USERS
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "[1/5] Menyiapkan users..." . PHP_EOL;

            $this->seedUsers();

            /*
             * ------------------------------------------------------------
             * 2. CUSTOMER
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "[2/5] Menyiapkan customer..." . PHP_EOL;

            $this->seedCustomers();

            /*
             * ------------------------------------------------------------
             * 3. ATURAN DENDA
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "[3/5] Menyiapkan aturan denda..." . PHP_EOL;

            $this->seedAturanDenda();

            /*
             * ------------------------------------------------------------
             * 4. PIUTANG
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "[4/5] Menyiapkan piutang..." . PHP_EOL;

            $piutangIds = $this->seedPiutang();

            /*
             * ------------------------------------------------------------
             * 5. PEMBAYARAN
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "[5/5] Menyiapkan pembayaran..." . PHP_EOL;

            $this->seedPembayaran($piutangIds);

            /*
             * ------------------------------------------------------------
             * SELESAI
             * ------------------------------------------------------------
             */
            echo PHP_EOL;
            echo "================================================" . PHP_EOL;
            echo " DATABASE DEMO BERHASIL DISEDIAKAN" . PHP_EOL;
            echo "================================================" . PHP_EOL;
            echo "Users             : 6" . PHP_EOL;
            echo "Customer          : 5" . PHP_EOL;
            echo "Versi Denda       : 3" . PHP_EOL;
            echo "Aturan Denda      : 15" . PHP_EOL;
            echo "Piutang           : 8" . PHP_EOL;
            echo "Pembayaran        : 6" . PHP_EOL;
            echo "================================================" . PHP_EOL;
            echo PHP_EOL;
        } catch (Throwable $e) {
            throw $e;
        }
    }

    // ====================================================================
    // USERS
    // ====================================================================

    private function seedUsers(): void
    {
        $provider = auth()->getProvider();

        $authGroups = config('AuthGroups');

        /*
         * Pastikan role tersedia.
         */
        foreach ($this->users as $data) {
            if (! isset($authGroups->groups[$data['group']])) {
                throw new RuntimeException(
                    'Group "' . $data['group']
                    . '" tidak ditemukan di AuthGroups.'
                );
            }
        }

        foreach ($this->users as $data) {
            /*
             * Cari berdasarkan email.
             */
            $user = $provider->findByCredentials([
                'email' => $data['email'],
            ]);

            /*
             * Buat jika belum ada.
             */
            if ($user === null) {
                $user = new User([
                    'username' => $data['username'],
                    'email'    => $data['email'],
                    'password' => $data['password'],
                ]);

                $provider->save($user);

                $user = $provider->findById(
                    $provider->getInsertID()
                );

                if ($user === null) {
                    throw new RuntimeException(
                        'Gagal mengambil user setelah dibuat: '
                        . $data['email']
                    );
                }
            } else {
                /*
                 * Pastikan username sesuai.
                 */
                $user->fill([
                    'username' => $data['username'],
                ]);

                $provider->save($user);
            }

            /*
             * Aktifkan user demo.
             */
            $user->activate();

            /*
             * Pastikan role tepat.
             */
            $user->syncGroups($data['group']);

            /*
             * admin.demo menjadi user audit utama.
             */
            if ($data['username'] === 'admin.demo') {
                $this->adminUserId = (int) $user->id;
            }

            echo sprintf(
                "  %-18s → %-9s ID=%d%s",
                $data['username'],
                $data['group'],
                $user->id,
                PHP_EOL
            );
        }

        if ($this->adminUserId <= 0) {
            throw new RuntimeException(
                'User admin.demo tidak berhasil ditemukan.'
            );
        }

        echo "  User audit utama: admin.demo "
            . "(ID {$this->adminUserId})"
            . PHP_EOL;
    }

    // ====================================================================
    // CUSTOMER
    // ====================================================================

    private function seedCustomers(): void
    {
        $db = db_connect();

        $customers = [
            [
                'kode_customer'     => 'CUST-DEMO-001',
                'nama'              => 'Budi Santoso',
                'nik'               => '3173000000000001',
                'no_hp'             => '081234560001',
                'alamat'            => 'Jakarta',
                'tanggal_terdaftar' => '2026-08-01',
                'status'            => 1,
            ],
            [
                'kode_customer'     => 'CUST-DEMO-002',
                'nama'              => 'Siti Rahma',
                'nik'               => '3173000000000002',
                'no_hp'             => '081234560002',
                'alamat'            => 'Bandung',
                'tanggal_terdaftar' => '2026-08-02',
                'status'            => 1,
            ],
            [
                'kode_customer'     => 'CUST-DEMO-003',
                'nama'              => 'Andi Wijaya',
                'nik'               => '3173000000000003',
                'no_hp'             => '081234560003',
                'alamat'            => 'Surabaya',
                'tanggal_terdaftar' => '2026-08-03',
                'status'            => 1,
            ],
            [
                'kode_customer'     => 'CUST-DEMO-004',
                'nama'              => 'Dewi Lestari',
                'nik'               => '3173000000000004',
                'no_hp'             => '081234560004',
                'alamat'            => 'Semarang',
                'tanggal_terdaftar' => '2026-08-04',
                'status'            => 1,
            ],
            [
                'kode_customer'     => 'CUST-DEMO-005',
                'nama'              => 'Rizky Pratama',
                'nik'               => '3173000000000005',
                'no_hp'             => '081234560005',
                'alamat'            => 'Yogyakarta',
                'tanggal_terdaftar' => '2026-08-05',
                'status'            => 1,
            ],
        ];

        foreach ($customers as $customer) {
            $existing = $db
                ->table('customer')
                ->where(
                    'kode_customer',
                    $customer['kode_customer']
                )
                ->get()
                ->getRowArray();

            $data = $customer;

            if ($existing === null) {
                $data['created_by'] = $this->adminUserId;
                $data['created_at'] = date('Y-m-d H:i:s');
                $data['updated_at'] = date('Y-m-d H:i:s');

                $db->table('customer')->insert($data);
            } else {
                $db
                    ->table('customer')
                    ->where('id', $existing['id'])
                    ->update([
                        'nama'              => $customer['nama'],
                        'nik'               => $customer['nik'],
                        'no_hp'             => $customer['no_hp'],
                        'alamat'            => $customer['alamat'],
                        'tanggal_terdaftar' => $customer['tanggal_terdaftar'],
                        'status'            => $customer['status'],
                        'updated_by'        => $this->adminUserId,
                        'updated_at'        => date('Y-m-d H:i:s'),
                    ]);
            }
        }

        echo "  Customer aktif: 5" . PHP_EOL;
    }

    // ====================================================================
    // ATURAN DENDA
    // ====================================================================

    private function seedAturanDenda(): void
    {
        $db = db_connect();

        $versions = [
            [
                'kode_versi'      => 'DENDA-V001',
                'nama_versi'      => 'Kebijakan Denda Periode Awal',
                'tanggal_mulai'   => '2026-01-01',
                'tanggal_selesai' => '2026-06-30',
                'status'          => 'selesai',
                'keterangan'      => 'Versi pertama untuk periode awal tahun 2026.',
            ],
            [
                'kode_versi'      => 'DENDA-V002',
                'nama_versi'      => 'Kebijakan Denda Periode Berjalan',
                'tanggal_mulai'   => '2026-07-01',
                'tanggal_selesai' => '2026-09-30',
                'status'          => 'aktif',
                'keterangan'      => 'Versi denda yang sedang berlaku pada periode Juli sampai September 2026.',
            ],
            [
                'kode_versi'      => 'DENDA-V003',
                'nama_versi'      => 'Kebijakan Denda Periode Berikutnya',
                'tanggal_mulai'   => '2026-10-01',
                'tanggal_selesai' => '2026-12-31',
                'status'          => 'draft',
                'keterangan'      => 'Versi denda yang akan berlaku mulai Oktober 2026.',
            ],
        ];

        $ranges = [
            'DENDA-V001' => [
                [1,        999999,  1.50, 30, 30],
                [1000000,  4999999, 2.00, 30, 40],
                [5000000,  9999999, 2.50, 30, 50],
                [10000000, 24999999, 3.00, 30, 60],
                [25000000, null,     3.50, 30, 70],
            ],

            'DENDA-V002' => [
                [1,        999999,  1.75, 30, 35],
                [1000000,  4999999, 2.25, 30, 45],
                [5000000,  9999999, 2.75, 30, 55],
                [10000000, 24999999, 3.25, 30, 65],
                [25000000, null,     3.75, 30, 75],
            ],

            'DENDA-V003' => [
                [1,        999999,  2.00, 30, 40],
                [1000000,  4999999, 2.50, 30, 50],
                [5000000,  9999999, 3.00, 30, 60],
                [10000000, 24999999, 3.50, 30, 70],
                [25000000, null,     4.00, 30, 80],
            ],
        ];

        foreach ($versions as $version) {
            $existing = $db
                ->table('aturan_denda_versi')
                ->where(
                    'kode_versi',
                    $version['kode_versi']
                )
                ->get()
                ->getRowArray();

            $versionData = [
                'kode_versi'      => $version['kode_versi'],
                'nama_versi'      => $version['nama_versi'],
                'tanggal_mulai'   => $version['tanggal_mulai'],
                'tanggal_selesai' => $version['tanggal_selesai'],
                'status'          => $version['status'],
                'keterangan'      => $version['keterangan'],
                'updated_by'      => $this->adminUserId,
                'updated_at'      => date('Y-m-d H:i:s'),
            ];

            if ($existing === null) {
                $versionData['created_by'] =
                    $this->adminUserId;

                $versionData['created_at'] =
                    date('Y-m-d H:i:s');

                $db
                    ->table('aturan_denda_versi')
                    ->insert($versionData);

                $versionId = (int) $db->insertID();
            } else {
                $db
                    ->table('aturan_denda_versi')
                    ->where('id', $existing['id'])
                    ->update($versionData);

                $versionId = (int) $existing['id'];

                /*
                 * Untuk data demo, rentang dibuat ulang.
                 */
                $db
                    ->table('aturan_denda')
                    ->where('versi_id', $versionId)
                    ->delete();
            }

            foreach (
                $ranges[$version['kode_versi']]
                as $index => $range
            ) {
                [
                    $min,
                    $max,
                    $percentage,
                    $period,
                    $maximum,
                ] = $range;

                $names = [
                    'Denda Nominal s/d Rp999.999',
                    'Denda Nominal Rp1 Juta - Rp4.999.999',
                    'Denda Nominal Rp5 Juta - Rp9.999.999',
                    'Denda Nominal Rp10 Juta - Rp24.999.999',
                    'Denda Nominal Rp25 Juta ke Atas',
                ];

                $db
                    ->table('aturan_denda')
                    ->insert([
                        'versi_id' =>
                            $versionId,

                        'nama_aturan' =>
                            $names[$index],

                        'min_nominal' =>
                            $min,

                        'max_nominal' =>
                            $max,

                        'persentase_denda' =>
                            $percentage,

                        'periode_hari' =>
                            $period,

                        'maksimal_denda_persen' =>
                            $maximum,

                        'keterangan' =>
                            'Seed data '
                            . $version['kode_versi'],

                        'created_by' =>
                            $this->adminUserId,

                        'created_at' =>
                            date('Y-m-d H:i:s'),

                        'updated_at' =>
                            date('Y-m-d H:i:s'),
                    ]);
            }
        }

        echo "  Versi denda : 3" . PHP_EOL;
        echo "  Aturan denda: 15" . PHP_EOL;
    }

    // ====================================================================
    // PIUTANG
    // ====================================================================

    private function seedPiutang(): array
    {
        $customerModel = new CustomerModel();
        $piutangModel  = new PiutangModel();

        $customers = $customerModel
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->findAll();

        if (count($customers) < 5) {
            throw new RuntimeException(
                'DatabaseSeeder membutuhkan minimal 5 customer aktif.'
            );
        }

        $piutangData = [
            [
                'kode' => 'DEMO-PIU-001',
                'customer' => $customers[0],
                'tanggal_piutang' => '2026-08-01',
                'tanggal_jatuh_tempo' => '2026-08-25',
                'nominal_pokok' => 2000000,
                'persentase_bunga' => 2.00,
                'keterangan' => 'Demo - piutang belum jatuh tempo.',
            ],
            [
                'kode' => 'DEMO-PIU-002',
                'customer' => $customers[1],
                'tanggal_piutang' => '2026-08-05',
                'tanggal_jatuh_tempo' => '2026-08-30',
                'nominal_pokok' => 7000000,
                'persentase_bunga' => 2.50,
                'keterangan' => 'Demo - piutang nominal menengah belum jatuh tempo.',
            ],
            [
                'kode' => 'DEMO-PIU-003',
                'customer' => $customers[2],
                'tanggal_piutang' => '2026-06-15',
                'tanggal_jatuh_tempo' => '2026-07-15',
                'nominal_pokok' => 3000000,
                'persentase_bunga' => 2.00,
                'keterangan' => 'Demo - piutang menunggak tanpa pembayaran.',
            ],
            [
                'kode' => 'DEMO-PIU-004',
                'customer' => $customers[3],
                'tanggal_piutang' => '2026-06-10',
                'tanggal_jatuh_tempo' => '2026-07-15',
                'nominal_pokok' => 8000000,
                'persentase_bunga' => 2.50,
                'keterangan' => 'Demo - pembayaran sebagian.',
            ],
            [
                'kode' => 'DEMO-PIU-005',
                'customer' => $customers[4],
                'tanggal_piutang' => '2026-05-20',
                'tanggal_jatuh_tempo' => '2026-07-01',
                'nominal_pokok' => 12000000,
                'persentase_bunga' => 3.00,
                'keterangan' => 'Demo - piutang dengan beberapa kali pembayaran.',
            ],
            [
                'kode' => 'DEMO-PIU-006',
                'customer' => $customers[0],
                'tanggal_piutang' => '2026-05-01',
                'tanggal_jatuh_tempo' => '2026-06-01',
                'nominal_pokok' => 5000000,
                'persentase_bunga' => 2.00,
                'keterangan' => 'Demo - piutang lunas.',
            ],
            [
                'kode' => 'DEMO-PIU-007',
                'customer' => $customers[1],
                'tanggal_piutang' => '2026-04-15',
                'tanggal_jatuh_tempo' => '2026-05-15',
                'nominal_pokok' => 20000000,
                'persentase_bunga' => 3.00,
                'keterangan' => 'Demo - transaksi historis dengan pembayaran sebagian.',
            ],
            [
                'kode' => 'DEMO-PIU-008',
                'customer' => $customers[2],
                'tanggal_piutang' => '2026-03-01',
                'tanggal_jatuh_tempo' => '2026-04-01',
                'nominal_pokok' => 30000000,
                'persentase_bunga' => 3.50,
                'keterangan' => 'Demo - transaksi historis yang sudah lunas.',
            ],
        ];

        $piutangIds = [];

        foreach ($piutangData as $item) {
            $existing = $piutangModel
                ->withDeleted()
                ->where(
                    'nomor_piutang',
                    $item['kode']
                )
                ->first();

            if ($existing !== null) {
                $piutangIds[$item['kode']] =
                    (int) $existing['id'];

                continue;
            }

            $versi = $this->getApplicableVersion(
                $item['tanggal_piutang']
            );

            if ($versi === null) {
                throw new RuntimeException(
                    'Versi denda tidak ditemukan untuk '
                    . $item['kode']
                );
            }

            $aturan = $this->getApplicableRule(
                (int) $versi['id'],
                (float) $item['nominal_pokok']
            );

            if ($aturan === null) {
                throw new RuntimeException(
                    'Aturan denda tidak ditemukan untuk '
                    . $item['kode']
                );
            }

            $nominalBunga = round(
                (
                    (float) $item['nominal_pokok']
                    * (float) $item['persentase_bunga']
                ) / 100,
                2
            );

            $id = $piutangModel->insert([
                'customer_id' =>
                    (int) $item['customer']['id'],

                'nomor_piutang' =>
                    $item['kode'],

                'tanggal_piutang' =>
                    $item['tanggal_piutang'],

                'tanggal_jatuh_tempo' =>
                    $item['tanggal_jatuh_tempo'],

                'nominal_pokok' =>
                    $item['nominal_pokok'],

                'persentase_bunga' =>
                    $item['persentase_bunga'],

                'nominal_bunga' =>
                    $nominalBunga,

                'denda_versi_id' =>
                    (int) $versi['id'],

                'persentase_denda' =>
                    (float) $aturan['persentase_denda'],

                'periode_denda_hari' =>
                    (int) $aturan['periode_hari'],

                'maksimal_denda_persen' =>
                    (float) $aturan['maksimal_denda_persen'],

                'keterangan' =>
                    $item['keterangan'],

                'created_by' =>
                    $this->adminUserId,

                'created_at' =>
                    date('Y-m-d H:i:s'),

                'updated_at' =>
                    date('Y-m-d H:i:s'),
            ], true);

            if ($id === false) {
                throw new RuntimeException(
                    'Gagal membuat '
                    . $item['kode']
                    . ': '
                    . implode(
                        '; ',
                        $piutangModel->errors()
                    )
                );
            }

            $piutangIds[$item['kode']] = (int) $id;
        }

        echo "  Piutang: 8" . PHP_EOL;

        return $piutangIds;
    }

    // ====================================================================
    // PEMBAYARAN
    // ====================================================================

    private function seedPembayaran(array $piutangIds): void
    {
        $piutangModel = new PiutangModel();

        $paymentService = new PaymentService(
            $piutangModel
        );

        /*
         * Jangan membuat ulang pembayaran demo jika sudah ada.
         */
        $db = db_connect();

        $demoPayments = [
            [
                'kode' => 'DEMO-PIU-004',
                'tanggal' => '2026-08-01',
                'nominal' => 3000000,
                'keterangan' => 'Pembayaran sebagian - demo.',
            ],
            [
                'kode' => 'DEMO-PIU-005',
                'tanggal' => '2026-07-15',
                'nominal' => 3000000,
                'keterangan' => 'Pembayaran pertama - demo.',
            ],
            [
                'kode' => 'DEMO-PIU-005',
                'tanggal' => '2026-08-05',
                'nominal' => 2000000,
                'keterangan' => 'Pembayaran kedua - demo.',
            ],
            [
                'kode' => 'DEMO-PIU-006',
                'tanggal' => '2026-06-15',
                'nominal' => null,
                'keterangan' => 'Pelunasan penuh - demo.',
                'lunas' => true,
            ],
            [
                'kode' => 'DEMO-PIU-007',
                'tanggal' => '2026-06-15',
                'nominal' => 5000000,
                'keterangan' => 'Pembayaran sebagian historis - demo.',
            ],
            [
                'kode' => 'DEMO-PIU-008',
                'tanggal' => '2026-05-01',
                'nominal' => null,
                'keterangan' => 'Pelunasan penuh historis - demo.',
                'lunas' => true,
            ],
        ];

        $created = 0;

        foreach ($demoPayments as $payment) {
            $piutangId = $piutangIds[$payment['kode']];

            /*
             * Cek apakah pembayaran demo untuk piutang/tanggal/
             * keterangan sudah ada.
             */
            $existing = $db
                ->table('pembayaran')
                ->where(
                    'piutang_id',
                    $piutangId
                )
                ->where(
                    'tanggal_pembayaran',
                    $payment['tanggal']
                )
                ->where(
                    'keterangan',
                    $payment['keterangan']
                )
                ->where(
                    'status',
                    'valid'
                )
                ->get()
                ->getRowArray();

            if ($existing !== null) {
                continue;
            }

            if (
                ! empty($payment['lunas'])
            ) {
                $calculation =
                    $paymentService->calculatePayment(
                        $piutangId,
                        $payment['tanggal']
                    );

                $nominal =
                    (float) $calculation['total_tagihan'];
            } else {
                $nominal =
                    (float) $payment['nominal'];
            }

            $paymentService->createPayment(
                $piutangId,
                $payment['tanggal'],
                $nominal,
                $this->adminUserId,
                $payment['keterangan']
            );

            $created++;
        }

        /*
         * Pastikan total pembayaran demo adalah 6.
         */
        $totalDemoPayments = $db
            ->table('pembayaran')
            ->join(
                'piutang',
                'piutang.id = pembayaran.piutang_id'
            )
            ->like(
                'piutang.nomor_piutang',
                'DEMO-PIU-',
                'after'
            )
            ->where(
                'pembayaran.status',
                'valid'
            )
            ->countAllResults();

        echo "  Pembayaran dibuat: {$created}" . PHP_EOL;
        echo "  Pembayaran demo : {$totalDemoPayments}" . PHP_EOL;

        if ($totalDemoPayments !== 6) {
            throw new RuntimeException(
                'Jumlah pembayaran demo tidak sesuai. '
                . 'Diharapkan 6, ditemukan '
                . $totalDemoPayments
                . '.'
            );
        }
    }

    // ====================================================================
    // HELPER: VERSI DENDA
    // ====================================================================

    private function getApplicableVersion(
        string $tanggal
    ): ?array {
        $model = new AturanDendaVersiModel();

        return $model
            ->whereIn('status', [
                AturanDendaVersiModel::STATUS_AKTIF,
                AturanDendaVersiModel::STATUS_SELESAI,
            ])
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
            ->where(
                'deleted_at IS NULL',
                null,
                false
            )
            ->orderBy(
                'tanggal_mulai',
                'DESC'
            )
            ->first();
    }

    // ====================================================================
    // HELPER: ATURAN DENDA
    // ====================================================================

    private function getApplicableRule(
        int $versiId,
        float $nominal
    ): ?array {
        $model = new AturanDendaModel();

        $rules = $model
            ->where(
                'versi_id',
                $versiId
            )
            ->where(
                'deleted_at IS NULL',
                null,
                false
            )
            ->orderBy(
                'min_nominal',
                'ASC'
            )
            ->findAll();

        foreach ($rules as $rule) {
            $min = (float) $rule['min_nominal'];

            $max =
                $rule['max_nominal'] !== null
                    ? (float) $rule['max_nominal']
                    : null;

            if ($nominal < $min) {
                continue;
            }

            if (
                $max !== null
                && $nominal > $max
            ) {
                continue;
            }

            return $rule;
        }

        return null;
    }
}