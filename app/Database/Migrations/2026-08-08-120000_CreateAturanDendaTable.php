<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAturanDendaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([

            // ----------------------------------------------------------
            // Primary Key
            // ----------------------------------------------------------

            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // ----------------------------------------------------------
            // Informasi Aturan
            // ----------------------------------------------------------

            'nama_aturan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            // ----------------------------------------------------------
            // Rentang Nominal Pokok
            // ----------------------------------------------------------

            'min_nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],

            'max_nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],

            // ----------------------------------------------------------
            // Persentase dan Periode Denda
            // ----------------------------------------------------------

            'persentase_denda' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],

            'periode_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 30,
            ],

            'maksimal_denda_persen' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 100,
            ],

            // ----------------------------------------------------------
            // Periode Berlaku
            // ----------------------------------------------------------

            'tanggal_mulai' => [
                'type' => 'DATE',
            ],

            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
            ],

            // ----------------------------------------------------------
            // Status
            // ----------------------------------------------------------

            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = Aktif, 0 = Tidak Aktif',
            ],

            // ----------------------------------------------------------
            // Keterangan
            // ----------------------------------------------------------

            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            // ----------------------------------------------------------
            // Audit Trail
            // ----------------------------------------------------------

            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'deleted_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            // ----------------------------------------------------------
            // Timestamp
            // ----------------------------------------------------------

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // --------------------------------------------------------------
        // Primary Key
        // --------------------------------------------------------------

        $this->forge->addKey('id', true);

        // --------------------------------------------------------------
        // Index
        // --------------------------------------------------------------

        $this->forge->addKey('min_nominal');

        $this->forge->addKey('max_nominal');

        $this->forge->addKey('status');

        $this->forge->addKey('tanggal_mulai');

        $this->forge->addKey('tanggal_selesai');

        // --------------------------------------------------------------
        // Create Table
        // --------------------------------------------------------------

        $this->forge->createTable('aturan_denda');
    }

    public function down()
    {
        $this->forge->dropTable('aturan_denda');
    }
}