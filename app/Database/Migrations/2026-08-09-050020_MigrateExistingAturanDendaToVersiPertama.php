<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateExistingAturanDendaToVersiPertama extends Migration
{
    public function up()
    {
        $db = $this->db;

        $db->transStart();

        // ----------------------------------------------------------
        // 1. Cek apakah DENDA-V001 sudah ada
        // ----------------------------------------------------------

        $versi = $db->table('aturan_denda_versi')
            ->where('kode_versi', 'DENDA-V001')
            ->get()
            ->getRowArray();


        // ----------------------------------------------------------
        // 2. Jika belum ada, buat versi pertama
        // ----------------------------------------------------------

        if ($versi === null) {

            $db->table('aturan_denda_versi')->insert([
                'kode_versi'      => 'DENDA-V001',
                'nama_versi'      => 'Kebijakan Denda Awal',
                'tanggal_mulai'   => '2026-08-08',
                'tanggal_selesai' => null,
                'status'          => 'aktif',
                'keterangan'      => 'Versi pertama aturan denda.',
                'created_at'      => date('Y-m-d H:i:s'),
                'updated_at'      => date('Y-m-d H:i:s'),
            ]);

            $versiId = $db->insertID();

        } else {

            $versiId = $versi['id'];

        }


        // ----------------------------------------------------------
        // 3. Hubungkan seluruh aturan denda existing
        //    yang belum memiliki versi
        // ----------------------------------------------------------

        $db->table('aturan_denda')
            ->where('versi_id IS NULL', null, false)
            ->update([
                'versi_id' => $versiId,
            ]);


        $db->transComplete();


        // ----------------------------------------------------------
        // 4. Pastikan transaction berhasil
        // ----------------------------------------------------------

        if ($db->transStatus() === false) {
            throw new \RuntimeException(
                'Migrasi aturan denda ke DENDA-V001 gagal.'
            );
        }
    }


    public function down()
    {
        $db = $this->db;

        $versi = $db->table('aturan_denda_versi')
            ->where('kode_versi', 'DENDA-V001')
            ->get()
            ->getRowArray();

        if ($versi === null) {
            return;
        }


        // Lepaskan relasi aturan denda
        $db->table('aturan_denda')
            ->where('versi_id', $versi['id'])
            ->update([
                'versi_id' => null,
            ]);


        // Hapus versi pertama
        $db->table('aturan_denda_versi')
            ->where('id', $versi['id'])
            ->delete();
    }
}