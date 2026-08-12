<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use RuntimeException;
use Throwable;

class DemoAturanDendaSeeder extends Seeder
{
    /**
     * User yang digunakan sebagai pembuat data demo.
     */
    protected int $userId = 1;

    /**
     * Jalankan seeder.
     */
    public function run()
    {
        $db = db_connect();

        $db->transBegin();

        try {

            // ============================================================
            // 1. HAPUS DATA DEMO ATURAN DENDA SEBELUMNYA
            // ============================================================
            //
            // Hanya data dengan kode DEMO-Vxxx yang disentuh.
            // Data aturan denda lainnya tetap aman.
            //

            $versions = $db
                ->table('aturan_denda_versi')
                ->select('id')
                ->like('kode_versi', 'DEMO-V', 'after')
                ->get()
                ->getResultArray();

            $versionIds = array_map(
                static fn ($row) => (int) $row['id'],
                $versions
            );

            if (! empty($versionIds)) {

                // Hapus detail/range terlebih dahulu
                // karena memiliki FK ke aturan_denda_versi.
                $db->table('aturan_denda')
                    ->whereIn('versi_id', $versionIds)
                    ->delete();

                // Kemudian hapus versi.
                $db->table('aturan_denda_versi')
                    ->whereIn('id', $versionIds)
                    ->delete();
            }


            // ============================================================
            // 2. DATA 3 VERSI
            // ============================================================

            $versions = [

                /*
                 * V001
                 * Status: SELESAI
                 */
                [
                    'kode_versi' =>
                        'DEMO-V001',

                    'nama_versi' =>
                        'Kebijakan Denda Periode 1',

                    'tanggal_mulai' =>
                        '2026-01-01',

                    'tanggal_selesai' =>
                        '2026-04-30',

                    'keterangan' =>
                        'Data demo - kebijakan denda Januari sampai April 2026.',
                ],

                /*
                 * V002
                 * Status: AKTIF
                 */
                [
                    'kode_versi' =>
                        'DEMO-V002',

                    'nama_versi' =>
                        'Kebijakan Denda Periode 2',

                    'tanggal_mulai' =>
                        '2026-05-01',

                    'tanggal_selesai' =>
                        '2026-08-31',

                    'keterangan' =>
                        'Data demo - kebijakan denda Mei sampai Agustus 2026.',
                ],

                /*
                 * V003
                 * Status: AKAN DATANG
                 */
                [
                    'kode_versi' =>
                        'DEMO-V003',

                    'nama_versi' =>
                        'Kebijakan Denda Periode 3',

                    'tanggal_mulai' =>
                        '2026-09-01',

                    'tanggal_selesai' =>
                        '2026-11-30',

                    'keterangan' =>
                        'Data demo - kebijakan denda September sampai November 2026.',
                ],
            ];


            // ============================================================
            // 3. 5 RENTANG NOMINAL
            // ============================================================

            $ranges = [

                [
                    'nama_aturan' =>
                        'Rentang 1',

                    'min_nominal' =>
                        0,

                    'max_nominal' =>
                        2500000,
                ],

                [
                    'nama_aturan' =>
                        'Rentang 2',

                    'min_nominal' =>
                        2500000.01,

                    'max_nominal' =>
                        5000000,
                ],

                [
                    'nama_aturan' =>
                        'Rentang 3',

                    'min_nominal' =>
                        5000000.01,

                    'max_nominal' =>
                        10000000,
                ],

                [
                    'nama_aturan' =>
                        'Rentang 4',

                    'min_nominal' =>
                        10000000.01,

                    'max_nominal' =>
                        25000000,
                ],

                [
                    'nama_aturan' =>
                        'Rentang 5',

                    'min_nominal' =>
                        25000000.01,

                    'max_nominal' =>
                        null,
                ],
            ];


            // ============================================================
            // 4. PERSENTASE DENDA PER VERSI
            // ============================================================

            $percentages = [

                'DEMO-V001' => [
                    0.50,
                    0.75,
                    1.00,
                    1.25,
                    1.50,
                ],

                'DEMO-V002' => [
                    0.60,
                    0.85,
                    1.10,
                    1.35,
                    1.60,
                ],

                'DEMO-V003' => [
                    0.70,
                    0.95,
                    1.20,
                    1.45,
                    1.75,
                ],
            ];


            // ============================================================
            // 5. MAKSIMAL DENDA PER VERSI
            // ============================================================

            $maxPenalty = [

                'DEMO-V001' =>
                    10.00,

                'DEMO-V002' =>
                    12.50,

                'DEMO-V003' =>
                    15.00,
            ];


            // ============================================================
            // 6. INSERT VERSI DAN 5 RENTANG
            // ============================================================

            foreach ($versions as $version) {

                // --------------------------------------------------------
                // Insert versi
                // --------------------------------------------------------

                $versionData = [

                    'kode_versi' =>
                        $version['kode_versi'],

                    'nama_versi' =>
                        $version['nama_versi'],

                    'tanggal_mulai' =>
                        $version['tanggal_mulai'],

                    'tanggal_selesai' =>
                        $version['tanggal_selesai'],

                    'keterangan' =>
                        $version['keterangan'],

                    'created_by' =>
                        $this->userId,
                ];


                $inserted =
                    $db->table('aturan_denda_versi')
                        ->insert($versionData);


                if (! $inserted) {

                    throw new RuntimeException(
                        'Gagal membuat versi '
                        . $version['kode_versi']
                    );
                }


                /*
                 * Ambil ID dari connection, BUKAN dari Builder.
                 */
                $versionId =
                    (int) $db->insertID();


                if ($versionId <= 0) {

                    throw new RuntimeException(
                        'ID versi tidak berhasil diperoleh untuk '
                        . $version['kode_versi']
                    );
                }


                // --------------------------------------------------------
                // Insert 5 rentang
                // --------------------------------------------------------

                foreach (
                    $ranges as $index => $range
                ) {

                    $percentage =
                        $percentages[
                            $version['kode_versi']
                        ][$index];


                    $ruleData = [

                        'versi_id' =>
                            $versionId,

                        'nama_aturan' =>
                            $range['nama_aturan'],

                        'min_nominal' =>
                            $range['min_nominal'],

                        'max_nominal' =>
                            $range['max_nominal'],

                        'persentase_denda' =>
                            $percentage,

                        'periode_hari' =>
                            30,

                        'maksimal_denda_persen' =>
                            $maxPenalty[
                                $version['kode_versi']
                            ],

                        'keterangan' =>
                            'Data demo '
                            . $version['kode_versi']
                            . ' - '
                            . $range['nama_aturan'],

                        'created_by' =>
                            $this->userId,
                    ];


                    $inserted =
                        $db->table('aturan_denda')
                            ->insert($ruleData);


                    if (! $inserted) {

                        throw new RuntimeException(
                            'Gagal membuat '
                            . $range['nama_aturan']
                            . ' untuk '
                            . $version['kode_versi']
                        );
                    }
                }
            }


            // ============================================================
            // 7. VALIDASI TRANSACTION
            // ============================================================

            if (! $db->transStatus()) {

                throw new RuntimeException(
                    'Transaksi seed data gagal.'
                );
            }


            $db->transCommit();


            // ============================================================
            // 8. OUTPUT
            // ============================================================

            echo PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo " DEMO ATURAN DENDA BERHASIL DIBUAT" . PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo "Versi        : 3" . PHP_EOL;
            echo "Rentang/versi: 5" . PHP_EOL;
            echo "Total aturan : 15" . PHP_EOL;
            echo PHP_EOL;
            echo "DEMO-V001    : SELESAI" . PHP_EOL;
            echo "DEMO-V002    : AKTIF" . PHP_EOL;
            echo "DEMO-V003    : AKAN DATANG" . PHP_EOL;
            echo "============================================" . PHP_EOL;
            echo PHP_EOL;

        } catch (Throwable $e) {

            $db->transRollback();

            throw $e;
        }
    }
}