<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForeignKeyAturanDendaVersi extends Migration
{
    public function up()
    {
        // Pastikan seluruh data sudah memiliki versi.
        $count = $this->db
            ->table('aturan_denda')
            ->where('versi_id IS NULL', null, false)
            ->countAllResults();

        if ($count > 0) {
            throw new \RuntimeException(
                'Tidak dapat melanjutkan migration: masih ada aturan_denda dengan versi_id NULL.'
            );
        }

        // Ubah versi_id menjadi NOT NULL.
        $this->db->query(
            'ALTER TABLE `aturan_denda`
             MODIFY `versi_id` INT(11) UNSIGNED NOT NULL'
        );

        // Tambahkan foreign key.
        $this->db->query(
            'ALTER TABLE `aturan_denda`
             ADD CONSTRAINT `fk_aturan_denda_versi`
             FOREIGN KEY (`versi_id`)
             REFERENCES `aturan_denda_versi` (`id`)
             ON UPDATE CASCADE
             ON DELETE RESTRICT'
        );
    }

    public function down()
    {
        // Hapus foreign key.
        $this->db->query(
            'ALTER TABLE `aturan_denda`
             DROP FOREIGN KEY `fk_aturan_denda_versi`'
        );

        // Kembalikan menjadi nullable.
        $this->db->query(
            'ALTER TABLE `aturan_denda`
             MODIFY `versi_id` INT(11) UNSIGNED NULL'
        );
    }
}