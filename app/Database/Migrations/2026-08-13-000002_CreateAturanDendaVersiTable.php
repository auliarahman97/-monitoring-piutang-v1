<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAturanDendaVersiTable extends Migration
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

        // Primary key
        $this->forge->addKey('id', true);

        // Unique
        $this->forge->addUniqueKey(
            'kode_versi',
            'uq_aturan_denda_versi_kode'
        );

        // Index
        $this->forge->addKey(
            'tanggal_mulai',
            false,
            false,
            'idx_aturan_denda_versi_mulai'
        );

        $this->forge->addKey(
            'tanggal_selesai',
            false,
            false,
            'idx_aturan_denda_versi_selesai'
        );

        $this->forge->addKey(
            'status',
            false,
            false,
            'idx_aturan_denda_versi_status'
        );

        // Audit foreign keys
        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_versi_created_by'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_versi_updated_by'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_aturan_denda_versi_deleted_by'
        );

        $this->forge->createTable(
            'aturan_denda_versi',
            true
        );
    }

    public function down(): void
    {
        $this->forge->dropTable(
            'aturan_denda_versi',
            true
        );
    }
}