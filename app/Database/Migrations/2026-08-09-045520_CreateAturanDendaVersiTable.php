<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAturanDendaVersiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'kode_versi' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],

            'nama_versi' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],

            'tanggal_mulai' => [
                'type' => 'DATE',
            ],

            'tanggal_selesai' => [
                'type' => 'DATE',
                'null' => true,
            ],

            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'draft',
            ],

            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

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

        $this->forge->addKey('id', true);

        $this->forge->addUniqueKey('kode_versi');

        $this->forge->addKey('tanggal_mulai');

        $this->forge->addKey('tanggal_selesai');

        $this->forge->addKey('status');

        $this->forge->createTable('aturan_denda_versi');
    }

    public function down()
    {
        $this->forge->dropTable('aturan_denda_versi', true);
    }
}