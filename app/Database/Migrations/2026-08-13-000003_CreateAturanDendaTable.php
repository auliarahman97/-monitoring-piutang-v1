<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAturanDendaTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            'versi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'nama_aturan' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'min_nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
            ],

            'max_nominal' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],

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

        // Primary key
        $this->forge->addKey('id', true);

        // Index
        $this->forge->addKey(
            'versi_id',
            false,
            false,
            'idx_aturan_denda_versi_id'
        );

        $this->forge->addKey(
            'min_nominal',
            false,
            false,
            'idx_aturan_denda_min_nominal'
        );

        $this->forge->addKey(
            'max_nominal',
            false,
            false,
            'idx_aturan_denda_max_nominal'
        );

        // FK versi
        $this->forge->addForeignKey(
            'versi_id',
            'aturan_denda_versi',
            'id',
            'RESTRICT',
            'CASCADE',
            'fk_aturan_denda_versi'
        );

        // FK audit
        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_created_by'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_updated_by'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_deleted_by'
        );

        $this->forge->createTable(
            'aturan_denda',
            true
        );
    }

    public function down(): void
    {
        $this->forge->dropTable(
            'aturan_denda',
            true
        );
    }
}