<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AturanDendaSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        /*
        |--------------------------------------------------------------------------
        | Konfigurasi Versi
        |--------------------------------------------------------------------------
        |
        | 09-08-2026:
        |
        | V001 = Selesai
        | V002 = Aktif
        | V003 = Akan Datang
        |
        */

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


        /*
        |--------------------------------------------------------------------------
        | Rentang Denda
        |--------------------------------------------------------------------------
        |
        | Setiap versi memiliki 5 rentang.
        |
        | 1.  Rp 1              - Rp 999.999
        | 2.  Rp 1.000.000      - Rp 4.999.999
        | 3.  Rp 5.000.000      - Rp 9.999.999
        | 4.  Rp 10.000.000     - Rp 24.999.999
        | 5.  Rp 25.000.000     - tanpa batas
        |
        */

        $ranges = [

            'DENDA-V001' => [

                [
                    'nama_aturan'           => 'Denda Nominal s/d Rp999.999',
                    'min_nominal'           => 1,
                    'max_nominal'           => 999999,
                    'persentase_denda'      => 1.50,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 30,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp1 Juta - Rp4.999.999',
                    'min_nominal'           => 1000000,
                    'max_nominal'           => 4999999,
                    'persentase_denda'      => 2.00,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 40,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp5 Juta - Rp9.999.999',
                    'min_nominal'           => 5000000,
                    'max_nominal'           => 9999999,
                    'persentase_denda'      => 2.50,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 50,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp10 Juta - Rp24.999.999',
                    'min_nominal'           => 10000000,
                    'max_nominal'           => 24999999,
                    'persentase_denda'      => 3.00,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 60,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp25 Juta ke Atas',
                    'min_nominal'           => 25000000,
                    'max_nominal'           => null,
                    'persentase_denda'      => 3.50,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 70,
                ],

            ],


            'DENDA-V002' => [

                [
                    'nama_aturan'           => 'Denda Nominal s/d Rp999.999',
                    'min_nominal'           => 1,
                    'max_nominal'           => 999999,
                    'persentase_denda'      => 1.75,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 35,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp1 Juta - Rp4.999.999',
                    'min_nominal'           => 1000000,
                    'max_nominal'           => 4999999,
                    'persentase_denda'      => 2.25,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 45,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp5 Juta - Rp9.999.999',
                    'min_nominal'           => 5000000,
                    'max_nominal'           => 9999999,
                    'persentase_denda'      => 2.75,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 55,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp10 Juta - Rp24.999.999',
                    'min_nominal'           => 10000000,
                    'max_nominal'           => 24999999,
                    'persentase_denda'      => 3.25,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 65,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp25 Juta ke Atas',
                    'min_nominal'           => 25000000,
                    'max_nominal'           => null,
                    'persentase_denda'      => 3.75,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 75,
                ],

            ],


            'DENDA-V003' => [

                [
                    'nama_aturan'           => 'Denda Nominal s/d Rp999.999',
                    'min_nominal'           => 1,
                    'max_nominal'           => 999999,
                    'persentase_denda'      => 2.00,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 40,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp1 Juta - Rp4.999.999',
                    'min_nominal'           => 1000000,
                    'max_nominal'           => 4999999,
                    'persentase_denda'      => 2.50,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 50,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp5 Juta - Rp9.999.999',
                    'min_nominal'           => 5000000,
                    'max_nominal'           => 9999999,
                    'persentase_denda'      => 3.00,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 60,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp10 Juta - Rp24.999.999',
                    'min_nominal'           => 10000000,
                    'max_nominal'           => 24999999,
                    'persentase_denda'      => 3.50,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 70,
                ],

                [
                    'nama_aturan'           => 'Denda Nominal Rp25 Juta ke Atas',
                    'min_nominal'           => 25000000,
                    'max_nominal'           => null,
                    'persentase_denda'      => 4.00,
                    'periode_hari'          => 30,
                    'maksimal_denda_persen' => 80,
                ],

            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Transaction
        |--------------------------------------------------------------------------
        */

        $db->transStart();


        /*
        |--------------------------------------------------------------------------
        | Seed Versi + Rentang
        |--------------------------------------------------------------------------
        */

        foreach ($versions as $version) {

            /*
            |--------------------------------------------------------------------------
            | Cari versi berdasarkan kode
            |--------------------------------------------------------------------------
            */

            $existingVersion = $db
                ->table('aturan_denda_versi')
                ->where(
                    'kode_versi',
                    $version['kode_versi']
                )
                ->get()
                ->getRowArray();


            /*
            |--------------------------------------------------------------------------
            | Insert / Update Versi
            |--------------------------------------------------------------------------
            */

            $versionData = [
                'kode_versi'      => $version['kode_versi'],
                'nama_versi'      => $version['nama_versi'],
                'tanggal_mulai'   => $version['tanggal_mulai'],
                'tanggal_selesai' => $version['tanggal_selesai'],
                'status'          => $version['status'],
                'keterangan'      => $version['keterangan'],
                'updated_at'      => date('Y-m-d H:i:s'),
            ];


            if ($existingVersion === null) {

                $versionData['created_at'] =
                    date('Y-m-d H:i:s');

                $db
                    ->table('aturan_denda_versi')
                    ->insert($versionData);

                $versionId =
                    (int) $db->insertID();

            } else {

                $db
                    ->table('aturan_denda_versi')
                    ->where(
                        'id',
                        $existingVersion['id']
                    )
                    ->update($versionData);

                $versionId =
                    (int) $existingVersion['id'];
            }


            /*
            |--------------------------------------------------------------------------
            | Bersihkan Rentang Lama
            |--------------------------------------------------------------------------
            |
            | Karena Seeder ini dimaksudkan sebagai data testing,
            | rentang untuk versi yang sama akan dibuat ulang.
            |
            */

            $db
                ->table('aturan_denda')
                ->where(
                    'versi_id',
                    $versionId
                )
                ->delete();


            /*
            |--------------------------------------------------------------------------
            | Insert Rentang
            |--------------------------------------------------------------------------
            */

            foreach (
                $ranges[$version['kode_versi']]
                as $range
            ) {

                $db
                    ->table('aturan_denda')
                    ->insert([
                        'versi_id'              => $versionId,
                        'nama_aturan'           => $range['nama_aturan'],
                        'min_nominal'           => $range['min_nominal'],
                        'max_nominal'           => $range['max_nominal'],
                        'persentase_denda'     => $range['persentase_denda'],
                        'periode_hari'          => $range['periode_hari'],
                        'maksimal_denda_persen'=> $range['maksimal_denda_persen'],

                        /*
                         * Field legacy.
                         *
                         * Tetap diisi agar database lama tetap
                         * memiliki data yang konsisten.
                         *
                         * Namun aplikasi versi baru menggunakan
                         * periode dari aturan_denda_versi.
                         */
                        'tanggal_mulai'         => $version['tanggal_mulai'],
                        'tanggal_selesai'       => $version['tanggal_selesai'],

                        /*
                         * Rule aktif secara internal.
                         */
                        'status'                => 1,

                        'keterangan'            =>
                            'Seed data ' . $version['kode_versi'],

                        'created_at'            =>
                            date('Y-m-d H:i:s'),

                        'updated_at'            =>
                            date('Y-m-d H:i:s'),
                    ]);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Complete Transaction
        |--------------------------------------------------------------------------
        */

        $db->transComplete();


        /*
        |--------------------------------------------------------------------------
        | Check Transaction
        |--------------------------------------------------------------------------
        */

        if ($db->transStatus() === false) {
            throw new \RuntimeException(
                'Seeder Aturan Denda gagal dijalankan.'
            );
        }
    }
}