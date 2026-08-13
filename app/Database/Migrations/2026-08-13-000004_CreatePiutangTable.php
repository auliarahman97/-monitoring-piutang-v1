<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePiutangTable extends Migration
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

            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'nomor_piutang' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],

            'tanggal_piutang' => [
                'type' => 'DATE',
            ],

            'tanggal_jatuh_tempo' => [
                'type' => 'DATE',
            ],

            'nominal_pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],

            'persentase_bunga' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],

            'nominal_bunga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],

            'denda_versi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'persentase_denda' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => 0,
            ],

            'periode_denda_hari' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
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

        // Unique
        $this->forge->addUniqueKey(
            'nomor_piutang',
            'uq_piutang_nomor'
        );

        // Index
        $this->forge->addKey(
            'customer_id',
            false,
            false,
            'idx_piutang_customer'
        );

        $this->forge->addKey(
            'denda_versi_id',
            false,
            false,
            'idx_piutang_denda_versi'
        );

        $this->forge->addKey(
            'tanggal_piutang',
            false,
            false,
            'idx_piutang_tanggal'
        );

        $this->forge->addKey(
            'tanggal_jatuh_tempo',
            false,
            false,
            'idx_piutang_jatuh_tempo'
        );

        // FK customer
        $this->forge->addForeignKey(
            'customer_id',
            'customer',
            'id',
            'RESTRICT',
            'RESTRICT',
            'fk_piutang_customer'
        );

        // FK versi denda
        $this->forge->addForeignKey(
            'denda_versi_id',
            'aturan_denda_versi',
            'id',
            'RESTRICT',
            'RESTRICT',
            'fk_piutang_denda_versi'
        );

        // FK audit
        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_piutang_created_by'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_piutang_updated_by'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_piutang_deleted_by'
        );

        $this->forge->createTable(
            'piutang',
            true
        );
    }

    public function down(): void
    {
        $this->forge->dropTable(
            'piutang',
            true
        );
    }
}