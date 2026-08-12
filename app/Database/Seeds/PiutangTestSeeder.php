<?php

declare(strict_types=1);

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PiutangTestSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        /*
         * ================================================================
         * 1. CUSTOMER TEST
         * ================================================================
         *
         * Pastikan tersedia beberapa customer aktif untuk pengujian.
         */

        $customers = [
            [
                'kode_customer'    => 'CUST-00001',
                'nama'             => 'Prabowo',
                'nik'              => '3173000000000001',
                'no_hp'            => '085487293892',
                'alamat'           => 'Jakarta',
                'tanggal_terdaftar' => '2026-08-01',
                'status'           => 1,
                'created_by'       => 1,
            ],
            [
                'kode_customer'    => 'CUST-00002',
                'nama'             => 'Soekarno',
                'nik'              => '3173000000000002',
                'no_hp'            => '08123456789',
                'alamat'           => 'Bandung',
                'tanggal_terdaftar' => '2026-08-01',
                'status'           => 1,
                'created_by'       => 1,
            ],
            [
                'kode_customer'    => 'CUST-00003',
                'nama'             => 'Megawati',
                'nik'              => '3173000000000003',
                'no_hp'            => '08223471238',
                'alamat'           => 'Surabaya',
                'tanggal_terdaftar' => '2026-08-01',
                'status'           => 1,
                'created_by'       => 1,
            ],
        ];

        /*
         * Insert customer hanya jika kode belum ada.
         */
        foreach ($customers as $customer) {

            $exists = $db->table('customer')
                ->where('kode_customer', $customer['kode_customer'])
                ->countAllResults();

            if ($exists === 0) {
                $db->table('customer')->insert($customer);
            }
        }

        /*
         * ================================================================
         * 2. ATURAN DENDA TEST
         * ================================================================
         *
         * Kita membuat tiga bracket untuk menguji otomatisasi pemilihan
         * aturan berdasarkan nominal piutang.
         *
         * 1 juta - 10 juta       = 2%
         * 10 juta - 50 juta      = 4%
         * 50 juta ke atas        = 6%
         *
         * Periode:
         * 30 hari
         *
         * Maksimum:
         * 100% dari pokok awal
         */

        $aturanDenda = [
            [
                'nama_aturan'          => 'Denda 1 - 10 Juta',
                'min_nominal'          => 1000000,
                'max_nominal'          => 10000000,
                'persentase_denda'     => 2,
                'periode_hari'         => 30,
                'maksimal_denda_persen' => 100,
                'tanggal_mulai'        => '2026-08-08',
                'tanggal_selesai'      => null,
                'status'               => 1,
                'keterangan'           => 'Aturan testing nominal 1 juta sampai 10 juta.',
                'created_by'           => 1,
            ],

            [
                'nama_aturan'          => 'Denda 10 - 50 Juta',
                'min_nominal'          => 10000001,
                'max_nominal'          => 50000000,
                'persentase_denda'     => 4,
                'periode_hari'         => 30,
                'maksimal_denda_persen' => 100,
                'tanggal_mulai'        => '2026-08-08',
                'tanggal_selesai'      => null,
                'status'               => 1,
                'keterangan'           => 'Aturan testing nominal 10 juta sampai 50 juta.',
                'created_by'           => 1,
            ],

            [
                'nama_aturan'          => 'Denda Diatas 50 Juta',
                'min_nominal'          => 50000001,
                'max_nominal'          => null,
                'persentase_denda'     => 6,
                'periode_hari'         => 30,
                'maksimal_denda_persen' => 100,
                'tanggal_mulai'        => '2026-08-08',
                'tanggal_selesai'      => null,
                'status'               => 1,
                'keterangan'           => 'Aturan testing nominal di atas 50 juta.',
                'created_by'           => 1,
            ],
        ];

        foreach ($aturanDenda as $aturan) {

            $exists = $db->table('aturan_denda')
                ->where('nama_aturan', $aturan['nama_aturan'])
                ->countAllResults();

            if ($exists === 0) {
                $db->table('aturan_denda')->insert($aturan);
            }
        }

        /*
         * ================================================================
         * SELESAI
         * ================================================================
         */
    }
}