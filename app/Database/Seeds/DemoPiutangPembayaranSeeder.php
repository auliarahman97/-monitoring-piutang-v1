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

class DemoPiutangPembayaranSeeder extends Seeder
{
    /**
     * User pembuat data demo.
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

        $piutangModel =
            new PiutangModel();

        $versiModel =
            new AturanDendaVersiModel();

        $aturanModel =
            new AturanDendaModel();

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
             * 1. VALIDASI USER
             * ============================================================
             */

            $user =
                $db->table('users')
                    ->where(
                        'id',
                        $this->userId
                    )
                    ->get()
                    ->getRowArray();

            if ($user === null) {
                throw new RuntimeException(
                    'User ID '
                    . $this->userId
                    . ' tidak ditemukan.'
                );
            }


            /*
             * ============================================================
             * 2. AMBIL CUSTOMER AKTIF
             * ============================================================
             *
             * Seeder tidak membuat customer baru.
             *
             * Kita menggunakan customer yang sudah ada.
             */

            $customers =
                $customerModel
                    ->where(
                        'status',
                        1
                    )
                    ->orderBy(
                        'id',
                        'ASC'
                    )
                    ->findAll();


            if (count($customers) < 5) {

                throw new RuntimeException(
                    'Minimal 5 customer aktif diperlukan '
                    . 'untuk menjalankan seeder demo.'
                );
            }


            /*
             * ============================================================
             * 3. PASTIKAN ATURAN DENDA SUDAH ADA
             * ============================================================
             */

            $versiAwal =
                $versiModel
                    ->where(
                        'kode_versi',
                        'DENDA-V001'
                    )
                    ->first();

            $versiBerjalan =
                $versiModel
                    ->where(
                        'kode_versi',
                        'DENDA-V002'
                    )
                    ->first();


            if ($versiAwal === null) {

                throw new RuntimeException(
                    'DENDA-V001 belum tersedia. '
                    . 'Jalankan AturanDendaSeeder terlebih dahulu.'
                );
            }


            if ($versiBerjalan === null) {

                throw new RuntimeException(
                    'DENDA-V002 belum tersedia. '
                    . 'Jalankan AturanDendaSeeder terlebih dahulu.'
                );
            }


            /*
             * ============================================================
             * 4. DATA PIUTANG
             * ============================================================
             *
             * Kita sengaja membuat kondisi yang berbeda-beda.
             */

            $piutangData = [

                /*
                 * --------------------------------------------------------
                 * PIU-001
                 * Belum jatuh tempo
                 * DENDA-V002
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-001',

                    'customer' =>
                        $customers[0],

                    'tanggal_piutang' =>
                        '2026-08-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-08-25',

                    'nominal_pokok' =>
                        2000000,

                    'persentase_bunga' =>
                        2.00,

                    'keterangan' =>
                        'Demo - piutang belum jatuh tempo.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-002
                 * Belum jatuh tempo
                 * DENDA-V002
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-002',

                    'customer' =>
                        $customers[1],

                    'tanggal_piutang' =>
                        '2026-08-05',

                    'tanggal_jatuh_tempo' =>
                        '2026-08-30',

                    'nominal_pokok' =>
                        7000000,

                    'persentase_bunga' =>
                        2.50,

                    'keterangan' =>
                        'Demo - piutang nominal menengah belum jatuh tempo.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-003
                 * Menunggak tanpa pembayaran
                 * DENDA-V002
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-003',

                    'customer' =>
                        $customers[2],

                    'tanggal_piutang' =>
                        '2026-06-15',

                    'tanggal_jatuh_tempo' =>
                        '2026-07-15',

                    'nominal_pokok' =>
                        3000000,

                    'persentase_bunga' =>
                        2.00,

                    'keterangan' =>
                        'Demo - piutang menunggak tanpa pembayaran.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-004
                 * Menunggak + pembayaran sebagian
                 * DENDA-V002
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-004',

                    'customer' =>
                        $customers[3],

                    'tanggal_piutang' =>
                        '2026-06-10',

                    'tanggal_jatuh_tempo' =>
                        '2026-07-15',

                    'nominal_pokok' =>
                        8000000,

                    'persentase_bunga' =>
                        2.50,

                    'keterangan' =>
                        'Demo - pembayaran sebagian.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-005
                 * Menunggak + beberapa pembayaran
                 * DENDA-V002
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-005',

                    'customer' =>
                        $customers[4],

                    'tanggal_piutang' =>
                        '2026-05-20',

                    'tanggal_jatuh_tempo' =>
                        '2026-07-01',

                    'nominal_pokok' =>
                        12000000,

                    'persentase_bunga' =>
                        3.00,

                    'keterangan' =>
                        'Demo - piutang dengan beberapa kali pembayaran.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-006
                 * LUNAS
                 * DENDA-V001
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-006',

                    'customer' =>
                        $customers[0],

                    'tanggal_piutang' =>
                        '2026-05-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-06-01',

                    'nominal_pokok' =>
                        5000000,

                    'persentase_bunga' =>
                        2.00,

                    'keterangan' =>
                        'Demo - piutang lunas.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-007
                 * Historis V001 + sebagian
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-007',

                    'customer' =>
                        $customers[1],

                    'tanggal_piutang' =>
                        '2026-04-15',

                    'tanggal_jatuh_tempo' =>
                        '2026-05-15',

                    'nominal_pokok' =>
                        20000000,

                    'persentase_bunga' =>
                        3.00,

                    'keterangan' =>
                        'Demo - transaksi historis dengan pembayaran sebagian.',
                ],


                /*
                 * --------------------------------------------------------
                 * PIU-008
                 * Historis V001 + lunas
                 * --------------------------------------------------------
                 */

                [
                    'kode' =>
                        'DEMO-PIU-008',

                    'customer' =>
                        $customers[2],

                    'tanggal_piutang' =>
                        '2026-03-01',

                    'tanggal_jatuh_tempo' =>
                        '2026-04-01',

                    'nominal_pokok' =>
                        30000000,

                    'persentase_bunga' =>
                        3.50,

                    'keterangan' =>
                        'Demo - transaksi historis yang sudah lunas.',
                ],
            ];


            /*
             * ============================================================
             * 5. INSERT PIUTANG
             * ============================================================
             */

            $piutangIds = [];


            foreach ($piutangData as $item) {

                /*
                 * --------------------------------------------------------
                 * Cek apakah sudah ada
                 * --------------------------------------------------------
                 */

                $existing =
                    $piutangModel
                        ->withDeleted()
                        ->where(
                            'nomor_piutang',
                            $item['kode']
                        )
                        ->first();


                if ($existing !== null) {

                    $piutangIds[
                        $item['kode']
                    ] =
                        (int) $existing['id'];

                    continue;
                }


                /*
                 * --------------------------------------------------------
                 * Cari versi denda berdasarkan tanggal piutang
                 * --------------------------------------------------------
                 */

                $versi =
                    $versiModel
                        ->getApplicableVersion(
                            $item['tanggal_piutang']
                        );


                if ($versi === null) {

                    throw new RuntimeException(
                        'Tidak ditemukan versi denda untuk '
                        . $item['kode']
                        . ' pada tanggal '
                        . $item['tanggal_piutang']
                    );
                }


                /*
                 * --------------------------------------------------------
                 * Cari aturan denda berdasarkan nominal
                 * --------------------------------------------------------
                 */

                $aturan =
                    $aturanModel
                        ->getApplicableRule(
                            (int) $versi['id'],
                            (float) $item['nominal_pokok']
                        );


                if ($aturan === null) {

                    throw new RuntimeException(
                        'Tidak ditemukan aturan denda untuk '
                        . $item['kode']
                    );
                }


                /*
                 * --------------------------------------------------------
                 * Hitung bunga
                 * --------------------------------------------------------
                 */

                $nominalBunga =
                    $piutangModel
                        ->calculateInterest(
                            $item['nominal_pokok'],
                            $item['persentase_bunga']
                        );


                /*
                 * --------------------------------------------------------
                 * Data piutang
                 * --------------------------------------------------------
                 */

                $data = [

                    'customer_id' =>
                        (int) $item[
                            'customer'
                        ]['id'],

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

                    /*
                     * Snapshot aturan denda.
                     */
                    'denda_versi_id' =>
                        (int) $versi['id'],

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


                /*
                 * --------------------------------------------------------
                 * Insert
                 * --------------------------------------------------------
                 */

                $id =
                    $piutangModel->insert(
                        $data,
                        true
                    );


                if ($id === false) {

                    throw new RuntimeException(
                        'Gagal membuat '
                        . $item['kode']
                        . ': '
                        . implode(
                            '; ',
                            $piutangModel
                                ->errors()
                        )
                    );
                }


                $piutangIds[
                    $item['kode']
                ] =
                    (int) $id;
            }


            /*
             * ============================================================
             * 6. PEMBAYARAN
             * ============================================================
             *
             * Semua pembayaran dibuat menggunakan PaymentService.
             *
             * Dengan demikian:
             *
             * Denda → Bunga → Pokok
             *
             * dihitung oleh sistem yang sama dengan transaksi normal.
             */


            /*
             * ------------------------------------------------------------
             * PIU-004
             * Pembayaran sebagian
             * ------------------------------------------------------------
             */

            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-004'],
                '2026-08-01',
                3000000,
                $this->userId,
                'Pembayaran sebagian - demo.'
            );


            /*
             * ------------------------------------------------------------
             * PIU-005
             * Pembayaran pertama
             * ------------------------------------------------------------
             */

            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-005'],
                '2026-07-15',
                3000000,
                $this->userId,
                'Pembayaran pertama - demo.'
            );


            /*
             * PIU-005
             * Pembayaran kedua
             */

            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-005'],
                '2026-08-05',
                2000000,
                $this->userId,
                'Pembayaran kedua - demo.'
            );


            /*
             * ------------------------------------------------------------
             * PIU-006
             * LUNAS
             * ------------------------------------------------------------
             *
             * Hitung total tagihan pada tanggal pembayaran,
             * lalu bayar tepat sebesar total tersebut.
             */

            $calculation =
                $paymentService->calculatePayment(
                    $piutangIds['DEMO-PIU-006'],
                    '2026-06-15'
                );


            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-006'],
                '2026-06-15',
                (float) $calculation[
                    'total_tagihan'
                ],
                $this->userId,
                'Pelunasan penuh - demo.'
            );


            /*
             * ------------------------------------------------------------
             * PIU-007
             * Pembayaran sebagian
             * ------------------------------------------------------------
             */

            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-007'],
                '2026-06-15',
                5000000,
                $this->userId,
                'Pembayaran sebagian historis - demo.'
            );


            /*
             * ------------------------------------------------------------
             * PIU-008
             * LUNAS
             * ------------------------------------------------------------
             */

            $calculation =
                $paymentService->calculatePayment(
                    $piutangIds['DEMO-PIU-008'],
                    '2026-05-01'
                );


            $paymentService->createPayment(
                $piutangIds['DEMO-PIU-008'],
                '2026-05-01',
                (float) $calculation[
                    'total_tagihan'
                ],
                $this->userId,
                'Pelunasan penuh historis - demo.'
            );


            /*
             * ============================================================
             * 7. VALIDASI TRANSACTION
             * ============================================================
             */

            if (
                $db->transStatus() === false
            ) {

                throw new RuntimeException(
                    'Transaction seeder gagal.'
                );
            }


            $db->transCommit();


            /*
             * ============================================================
             * 8. OUTPUT
             * ============================================================
             */

            echo PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo " DEMO PIUTANG & PEMBAYARAN BERHASIL" . PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo "Piutang     : 8 data" . PHP_EOL;
            echo "Pembayaran  : 7 transaksi" . PHP_EOL;
            echo PHP_EOL;
            echo "Kondisi:" . PHP_EOL;
            echo " - Belum jatuh tempo : 2" . PHP_EOL;
            echo " - Menunggak         : 3" . PHP_EOL;
            echo " - Lunas             : 2" . PHP_EOL;
            echo " - Historis V001     : 2" . PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo PHP_EOL;

        } catch (Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }
}