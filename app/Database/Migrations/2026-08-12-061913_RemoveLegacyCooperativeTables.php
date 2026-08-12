<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveLegacyCooperativeTables extends Migration
{
    public function up()
    {
        // Hapus child tables terlebih dahulu.
        $this->forge->dropTable('angsuran', true);
        $this->forge->dropTable('simpanan', true);

        // Kemudian tabel yang menjadi parent.
        $this->forge->dropTable('pinjaman', true);
        $this->forge->dropTable('anggota', true);
        $this->forge->dropTable('jenis_simpanan', true);
    }

    public function down()
    {
        /*
         * Legacy schema sengaja tidak direstore.
         *
         * Data legacy telah dipisahkan dari sistem aktif
         * dan harus tersedia melalui backup/archive database.
         */
        throw new \RuntimeException(
            'Legacy cooperative tables are intentionally not restored. ' .
            'Restore them from the archived database dump if required.'
        );
    }
}