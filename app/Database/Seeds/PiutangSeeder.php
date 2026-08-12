<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PiutangSeeder extends Seeder
{
    public function run()
    {
        $db = $this->db;

        /*
        |--------------------------------------------------------------------------
        | Ambil Customer Aktif
        |--------------------------------------------------------------------------
        */

        $customers = $db
            ->table('customer')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($customers)) {
            throw new \RuntimeException(
                'Seeder Piutang membutuhkan minimal 1 customer aktif.'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Data Piutang Testing
        |--------------------------------------------------------------------------
        |
        | Tanggal sengaja dibuat melewati tiga versi:
        |
        | V001 : 01-01-2026 s/d 30-06-2026
        | V002 : 01-07-2026 s/d 30-09-2026
        | V003 : 01-10-2026 s/d 31-12-2026
        |
        */

        $piutangData = [

            [
                'nomor_piutang'      => 'PIU-TEST-001',
                'tanggal_piutang'    => '2026-05-15',
                'tanggal_jatuh_tempo'=> '2026-06-14',
                'nominal_pokok'      => 750000,
                'persentase_bunga'   => 1.00,
                'keterangan'         => 'Testing V001 - rentang nominal di bawah Rp1 juta.',
            ],

            [
                'nomor_piutang'      => 'PIU-TEST-002',
                'tanggal_piutang'    => '2026-06-20',
                'tanggal_jatuh_tempo'=> '2026-07-20',
                'nominal_pokok'      => 7500000,
                'persentase_bunga'   => 1.50,
                'keterangan'         => 'Testing V001 - rentang nominal Rp5 juta sampai Rp9.999.999.',
            ],

            [
                'nomor_piutang'      => 'PIU-TEST-003',
                'tanggal_piutang'    => '2026-07-15',
                'tanggal_jatuh_tempo'=> '2026-08-14',
                'nominal_pokok'      => 3000000,
                'persentase_bunga'   => 2.00,
                'keterangan'         => 'Testing V002 - versi denda yang sedang aktif.',
            ],

            [
                'nomor_piutang'      => 'PIU-TEST-004',
                'tanggal_piutang'    => '2026-08-01',
                'tanggal_jatuh_tempo'=> '2026-08-31',
                'nominal_pokok'      => 12000000,
                'persentase_bunga'   => 2.00,
                'keterangan'         => 'Testing V002 - rentang nominal Rp10 juta sampai Rp24.999.999.',
            ],

            [
                'nomor_piutang'      => 'PIU-TEST-005',
                'tanggal_piutang'    => '2026-10-10',
                'tanggal_jatuh_tempo'=> '2026-11-10',
                'nominal_pokok'      => 800000,
                'persentase_bunga'   => 1.50,
                'keterangan'         => 'Testing V003 - versi denda yang akan datang.',
            ],

            [
                'nomor_piutang'      => 'PIU-TEST-006',
                'tanggal_piutang'    => '2026-10-15',
                'tanggal_jatuh_tempo'=> '2026-11-15',
                'nominal_pokok'      => 30000000,
                'persentase_bunga'   => 2.50,
                'keterangan'         => 'Testing V003 - rentang nominal Rp25 juta ke atas.',
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
        | Hapus Data Seed Sebelumnya
        |--------------------------------------------------------------------------
        |
        | Hanya menghapus data dengan nomor PIU-TEST-*.
        |
        | Data piutang lainnya tidak disentuh.
        |
        */

        $db
            ->table('piutang')
            ->where(
                'nomor_piutang LIKE',
                'PIU-TEST-%'
            )
            ->delete();


        /*
        |--------------------------------------------------------------------------
        | Insert Piutang
        |--------------------------------------------------------------------------
        */

        foreach ($piutangData as $index => $data) {

            /*
            |--------------------------------------------------------------------------
            | Pilih Customer
            |--------------------------------------------------------------------------
            |
            | Customer digunakan secara bergantian apabila tersedia
            | lebih dari satu customer.
            |
            */

            $customer =
                $customers[$index % count($customers)];


            /*
            |--------------------------------------------------------------------------
            | Cari Versi Denda
            |--------------------------------------------------------------------------
            */

            $versi = $db
                ->table('aturan_denda_versi')
                ->where(
                    'tanggal_mulai <=',
                    $data['tanggal_piutang']
                )
                ->groupStart()
                    ->where(
                        'tanggal_selesai >=',
                        $data['tanggal_piutang']
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
                ->orderBy(
                    'id',
                    'DESC'
                )
                ->get()
                ->getRowArray();


            if ($versi === null) {
                throw new \RuntimeException(
                    'Tidak ditemukan versi denda untuk tanggal '
                    . $data['tanggal_piutang']
                    . ' pada '
                    . $data['nomor_piutang']
                    . '.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Cari Rentang Nominal
            |--------------------------------------------------------------------------
            */

            $aturan = $db
                ->table('aturan_denda')
                ->where(
                    'versi_id',
                    $versi['id']
                )
                ->where(
                    'status',
                    1
                )
                ->orderBy(
                    'min_nominal',
                    'ASC'
                )
                ->get()
                ->getResultArray();


            $selectedRule = null;


            foreach ($aturan as $rule) {

                $min =
                    (float) $rule['min_nominal'];

                $max =
                    $rule['max_nominal'] !== null
                    && $rule['max_nominal'] !== ''
                        ? (float) $rule['max_nominal']
                        : null;


                if (
                    (float) $data['nominal_pokok']
                    < $min
                ) {
                    continue;
                }


                if (
                    $max !== null
                    && (float) $data['nominal_pokok'] > $max
                ) {
                    continue;
                }


                $selectedRule = $rule;

                break;
            }


            if ($selectedRule === null) {
                throw new \RuntimeException(
                    'Tidak ditemukan rentang denda untuk nominal '
                    . $data['nominal_pokok']
                    . ' pada '
                    . $data['nomor_piutang']
                    . '.'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Hitung Bunga
            |--------------------------------------------------------------------------
            */

            $nominalBunga =
                round(
                    (
                        (float) $data['nominal_pokok']
                        * (float) $data['persentase_bunga']
                    ) / 100,
                    2
                );


            /*
            |--------------------------------------------------------------------------
            | Insert
            |--------------------------------------------------------------------------
            */

            $db
                ->table('piutang')
                ->insert([

                    'customer_id' =>
                        $customer['id'],

                    'nomor_piutang' =>
                        $data['nomor_piutang'],

                    'tanggal_piutang' =>
                        $data['tanggal_piutang'],

                    'tanggal_jatuh_tempo' =>
                        $data['tanggal_jatuh_tempo'],

                    'nominal_pokok' =>
                        $data['nominal_pokok'],

                    'persentase_bunga' =>
                        $data['persentase_bunga'],

                    'nominal_bunga' =>
                        $nominalBunga,


                    /*
                     * ----------------------------------------------------------
                     * Versi Denda
                     * ----------------------------------------------------------
                     */

                    'denda_versi_id' =>
                        $versi['id'],


                    /*
                     * ----------------------------------------------------------
                     * Snapshot Aturan Denda
                     * ----------------------------------------------------------
                     */

                    'persentase_denda' =>
                        $selectedRule['persentase_denda'],

                    'periode_denda_hari' =>
                        $selectedRule['periode_hari'],

                    'maksimal_denda_persen' =>
                        $selectedRule['maksimal_denda_persen'],


                    /*
                     * ----------------------------------------------------------
                     * Keterangan
                     * ----------------------------------------------------------
                     */

                    'keterangan' =>
                        $data['keterangan'],


                    /*
                     * ----------------------------------------------------------
                     * Timestamp
                     * ----------------------------------------------------------
                     */

                    'created_at' =>
                        date('Y-m-d H:i:s'),

                    'updated_at' =>
                        date('Y-m-d H:i:s'),

                ]);
        }


        /*
        |--------------------------------------------------------------------------
        | Complete Transaction
        |--------------------------------------------------------------------------
        */

        $db->transComplete();


        /*
        |--------------------------------------------------------------------------
        | Transaction Check
        |--------------------------------------------------------------------------
        */

        if ($db->transStatus() === false) {
            throw new \RuntimeException(
                'Seeder Piutang gagal dijalankan.'
            );
        }
    }
}