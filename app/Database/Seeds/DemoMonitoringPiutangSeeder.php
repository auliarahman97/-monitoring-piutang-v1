<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use App\Models\AturanDendaModel;
use App\Models\AturanDendaVersiModel;
use App\Models\CustomerModel;
use App\Models\PiutangModel;
use App\Services\PaymentService;
use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

class DemoMonitoringPiutangSeeder extends Seeder
{
    /**
     * User yang digunakan sebagai pembuat data demo.
     *
     * Sesuaikan jika user ID 1 tidak tersedia.
     */
    protected int $userId = 1;


    /**
     * Jalankan seeder.
     */
    public function run()
    {
        $db = db_connect();

        $customerModel =
            new CustomerModel();

        $versiModel =
            new AturanDendaVersiModel();

        $aturanModel =
            new AturanDendaModel();

        $piutangModel =
            new PiutangModel();

        $paymentService =
            new PaymentService(
                $piutangModel
            );


        /*
         * ================================================================
         * TRANSACTION
         * ================================================================
         */

        $db->transBegin();


        try {

            /*
             * ============================================================
             * 1. ATURAN DENDA
             * ============================================================
             */

            $versiId =
                $versiModel->insert([
                    'kode_versi' =>
                        'DEMO-V001',

                    'nama_versi' =>
                        'Aturan Denda Demo Monitoring',

                    /*
                     * Sengaja dibuat berlaku sejak awal 2026.
                     *
                     * Data piutang demo akan menggunakan snapshot
                     * aturan ini secara langsung.
                     */
                    'tanggal_mulai' =>
                        '2026-01-01',

                    'tanggal_selesai' =>
                        null,

                    'keterangan' =>
                        'Data demo untuk pengujian Sistem Monitoring Piutang.',

                    'created_by' =>
                        $this->userId,
                ], true);


            if ($versiId === false) {
                throw new RuntimeException(
                    'Gagal membuat versi aturan denda demo.'
                );
            }


            /*
             * ------------------------------------------------------------
             * Rentang 1
             * Rp1 - Rp5.000.000
             * ------------------------------------------------------------
             */

            $aturanModel->insert([
                'versi_id' =>
                    (int) $versiId,

                'nama_aturan' =>
                    'Denda Nominal Kecil',

                'min_nominal' =>
                    1,

                'max_nominal' =>
                    5000000,

                'persentase_denda' =>
                    0.50,

                'periode_hari' =>
                    30,

                'maksimal_denda_persen' =>
                    10.00,

                'keterangan' =>
                    'Aturan demo untuk piutang sampai dengan Rp5 juta.',

                'created_by' =>
                    $this->userId,
            ]);


            /*
             * ------------------------------------------------------------
             * Rentang 2
             * > Rp5.000.000 - Rp10.000.000
             * ------------------------------------------------------------
             */

            $aturanModel->insert([
                'versi_id' =>
                    (int) $versiId,

                'nama_aturan' =>
                    'Denda Nominal Menengah',

                'min_nominal' =>
                    5000000.01,

                'max_nominal' =>
                    10000000,

                'persentase_denda' =>
                    0.75,

                'periode_hari' =>
                    30,

                'maksimal_denda_persen' =>
                    15.00,

                'keterangan' =>
                    'Aturan demo untuk piutang di atas Rp5 juta sampai Rp10 juta.',

                'created_by' =>
                    $this->userId,
            ]);


            /*
             * ------------------------------------------------------------
             * Rentang 3
             * > Rp10.000.000
             * ------------------------------------------------------------
             */

            $aturanModel->insert([
                'versi_id' =>
                    (int) $versiId,

                'nama_aturan' =>
                    'Denda Nominal Besar',

                'min_nominal' =>
                    10000000.01,

                'max_nominal' =>
                    null,

                'persentase_denda' =>
                    1.00,

                'periode_hari' =>
                    30,

                'maksimal_denda_persen' =>
                    20.00,

                'keterangan' =>
                    'Aturan demo untuk piutang di atas Rp10 juta.',

                'created_by' =>
                    $this->userId,
            ]);


            /*
             * ============================================================
             * 2. CUSTOMER
             * ============================================================
             */

            $customers = [

                [
                    'kode_customer' =>
                        'DEMO-CUST-001',

                    'nama' =>
                        'Budi Santoso',

                    'nik' =>
                        '3273010101800001',

                    'no_hp' =>
                        '081234567801',

                    'alamat' =>
                        'Bandung',

                    'tanggal_terdaftar' =>
                        '2026-01-05',

                    'status' =>
                        1,

                    'created_by' =>
                        $this->userId,
                ],

                [
                    'kode_customer' =>
                        'DEMO-CUST-002',

                    'nama' =>
                        'Siti Aminah',

                    'nik' =>
                        '3273010202800002',

                    'no_hp' =>
                        '081234567802',

                    'alamat' =>
                        'Jakarta',

                    'tanggal_terdaftar' =>
                        '2026-01-10',

                    'status' =>
                        1,

                    'created_by' =>
                        $this->userId,
                ],

                [
                    'kode_customer' =>
                        'DEMO-CUST-003',

                    'nama' =>
                        'Andi Wijaya',

                    'nik' =>
                        '3273010303750003',

                    'no_hp' =>
                        '081234567803',

                    'alamat' =>
                        'Bogor',

                    'tanggal_terdaftar' =>
                        '2026-02-01',

                    'status' =>
                        1,

                    'created_by' =>
                        $this->userId,
                ],

                [
                    'kode_customer' =>
                        'DEMO-CUST-004',

                    'nama' =>
                        'Dewi Lestari',

                    'nik' =>
                        '3273010404900004',

                    'no_hp' =>
                        '081234567804',

                    'alamat' =>
                        'Depok',

                    'tanggal_terdaftar' =>
                        '2026-02-15',

                    'status' =>
                        1,

                    'created_by' =>
                        $this->userId,
                ],

                [
                    'kode_customer' =>
                        'DEMO-CUST-005',

                    'nama' =>
                        'Rudi Hartono',

                    'nik' =>
                        '3273010505850005',

                    'no_hp' =>
                        '081234567805',

                    'alamat' =>
                        'Bekasi',

                    'tanggal_terdaftar' =>
                        '2026-03-01',

                    'status' =>
                        1,

                    'created_by' =>
                        $this->userId,
                ],
            ];


            $customerIds = [];


            foreach ($customers as $customer) {

                $existing =
                    $customerModel
                        ->withDeleted()
                        ->where(
                            'kode_customer',
                            $customer['kode_customer']
                        )
                        ->first();


                if ($existing !== null) {

                    $customerIds[] =
                        (int) $existing['id'];

                    continue;
                }


                $id =
                    $customerModel->insert(
                        $customer,
                        true
                    );


                if ($id === false) {
                    throw new RuntimeException(
                        'Gagal membuat customer demo: '
                        . $customer['nama']
                    );
                }


                $customerIds[] =
                    (int) $id;
            }


            /*
             * ============================================================
             * 3. PIUTANG
             * ============================================================
             *
             * Kita masukkan snapshot aturan denda langsung.
             *
             * Ini sengaja agar data demo tidak bergantung pada versi
             * aturan mana yang sedang aktif di database.
             */


            $piutangData = [

                /*
                 * --------------------------------------------------------
                 * PIU DEMO 1
                 * Belum jatuh tempo
                 * --------------------------------------------------------
                 */

                [
                    'nomor_piutang' =>
                        'DEMO-PIU-001',

                    'customer_id' =>
                        $customerIds[0],

                    'tanggal_piutang' =>
                        '2026-08-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-08-25',

                    'nominal_pokok' =>
                        3000000,

                    'persentase_bunga' =>
                        2.00,

                    'keterangan' =>
                        'Demo piutang belum jatuh tempo.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU DEMO 2
                 * Menunggak tanpa pembayaran
                 * --------------------------------------------------------
                 */

                [
                    'nomor_piutang' =>
                        'DEMO-PIU-002',

                    'customer_id' =>
                        $customerIds[1],

                    'tanggal_piutang' =>
                        '2026-06-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-07-01',

                    'nominal_pokok' =>
                        8000000,

                    'persentase_bunga' =>
                        2.00,

                    'keterangan' =>
                        'Demo piutang menunggak tanpa pembayaran.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU DEMO 3
                 * Pembayaran sebagian
                 * --------------------------------------------------------
                 */

                [
                    'nomor_piutang' =>
                        'DEMO-PIU-003',

                    'customer_id' =>
                        $customerIds[2],

                    'tanggal_piutang' =>
                        '2026-06-15',

                    'tanggal_jatuh_tempo' =>
                        '2026-07-15',

                    'nominal_pokok' =>
                        10000000,

                    'persentase_bunga' =>
                        2.50,

                    'keterangan' =>
                        'Demo piutang dengan pembayaran sebagian.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU DEMO 4
                 * Lunas
                 * --------------------------------------------------------
                 */

                [
                    'nomor_piutang' =>
                        'DEMO-PIU-004',

                    'customer_id' =>
                        $customerIds[3],

                    'tanggal_piutang' =>
                        '2026-05-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-06-01',

                    'nominal_pokok' =>
                        5000000,

                    'persentase_bunga' =>
                        1.50,

                    'keterangan' =>
                        'Demo piutang yang sudah lunas.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU DEMO 5
                 * Jatuh tempo / belum dibayar
                 * --------------------------------------------------------
                 */

                [
                    'nomor_piutang' =>
                        'DEMO-PIU-005',

                    'customer_id' =>
                        $customerIds[4],

                    'tanggal_piutang' =>
                        '2026-07-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-08-10',

                    'nominal_pokok' =>
                        15000000,

                    'persentase_bunga' =>
                        3.00,

                    'keterangan' =>
                        'Demo piutang nominal besar.',
                ],
            ];


            $piutangIds = [];


            foreach ($piutangData as $item) {

                $existing =
                    $piutangModel
                        ->withDeleted()
                        ->where(
                            'nomor_piutang',
                            $item['nomor_piutang']
                        )
                        ->first();


                if ($existing !== null) {

                    $piutangIds[] =
                        (int) $existing['id'];

                    continue;
                }


                /*
                 * Tentukan snapshot aturan berdasarkan nominal.
                 */

                $aturan =
                    $aturanModel
                        ->getApplicableRule(
                            (int) $versiId,
                            (float) $item['nominal_pokok']
                        );


                if ($aturan === null) {
                    throw new RuntimeException(
                        'Aturan denda demo tidak ditemukan untuk '
                        . $item['nomor_piutang']
                    );
                }


                /*
                 * Hitung bunga.
                 */

                $nominalBunga =
                    $piutangModel
                        ->calculateInterest(
                            (float) $item['nominal_pokok'],
                            (float) $item['persentase_bunga']
                        );


                $data = [

                    'customer_id' =>
                        $item['customer_id'],

                    'nomor_piutang' =>
                        $item['nomor_piutang'],

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

                    /*
                     * Snapshot aturan denda.
                     */
                    'denda_versi_id' =>
                        (int) $versiId,

                    'persentase_denda' =>
                        (float) $aturan[
                            'persentase_denda'
                        ],

                    'periode_denda_hari' =>
                        (int) $aturan[
                            'periode_hari'
                        ],

                    'maksimal_denda_persen' =>
                        (float) $aturan[
                            'maksimal_denda_persen'
                        ],

                    'keterangan' =>
                        $item['keterangan'],

                    'created_by' =>
                        $this->userId,
                ];


                $id =
                    $piutangModel->insert(
                        $data,
                        true
                    );


                if ($id === false) {
                    throw new RuntimeException(
                        'Gagal membuat piutang demo: '
                        . $item['nomor_piutang']
                    );
                }


                $piutangIds[] =
                    (int) $id;
            }


            /*
             * ============================================================
             * 4. PEMBAYARAN
             * ============================================================
             *
             * Gunakan PaymentService agar:
             *
             * Denda → Bunga → Pokok
             *
             * dihitung persis seperti transaksi normal.
             */


            /*
             * PIU-003
             * Pembayaran sebagian.
             */

            $paymentService->createPayment(
                $piutangIds[2],
                '2026-08-01',
                3000000,
                $this->userId,
                'Pembayaran sebagian - data demo.'
            );


            /*
             * PIU-004
             * Lunas.
             *
             * Kita ambil total tagihan pada tanggal pembayaran
             * lalu bayar penuh.
             */

            $calculation =
                $paymentService->calculatePayment(
                    $piutangIds[3],
                    '2026-06-15'
                );


            $paymentService->createPayment(
                $piutangIds[3],
                '2026-06-15',
                (float) $calculation[
                    'total_tagihan'
                ],
                $this->userId,
                'Pelunasan penuh - data demo.'
            );


            /*
             * ============================================================
             * COMMIT
             * ============================================================
             */

            if (
                $db->transStatus() === false
            ) {
                throw new RuntimeException(
                    'Transaksi seed data gagal.'
                );
            }


            $db->transCommit();


            echo PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo " DEMO MONITORING PIUTANG BERHASIL" . PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo "Versi denda : DEMO-V001" . PHP_EOL;
            echo "Customer    : 5 data" . PHP_EOL;
            echo "Piutang     : 5 data" . PHP_EOL;
            echo "Pembayaran  : 2 transaksi" . PHP_EOL;
            echo "========================================" . PHP_EOL;
            echo PHP_EOL;

        } catch (Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }
}