<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
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

            'piutang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            'nomor_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],

            'tanggal_pembayaran' => [
                'type' => 'DATE',
            ],

            'total_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'nominal_pembayaran' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'alokasi_denda' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'alokasi_bunga' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'alokasi_pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'sisa_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'valid',
            ],

            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'alasan_pembatalan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'cancelled_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],

            'cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Primary key
        $this->forge->addKey('id', true);

        // Unique
        $this->forge->addUniqueKey(
            'nomor_pembayaran',
            'uq_pembayaran_nomor'
        );

        // Index
        $this->forge->addKey(
            'piutang_id',
            false,
            false,
            'idx_pembayaran_piutang'
        );

        $this->forge->addKey(
            'tanggal_pembayaran',
            false,
            false,
            'idx_pembayaran_tanggal'
        );

        $this->forge->addKey(
            'status',
            false,
            false,
            'idx_pembayaran_status'
        );

        // FK piutang
        $this->forge->addForeignKey(
            'piutang_id',
            'piutang',
            'id',
            'RESTRICT',
            'RESTRICT',
            'fk_pembayaran_piutang'
        );

        // FK audit
        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_pembayaran_created_by'
        );

        $this->forge->addForeignKey(
            'cancelled_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_pembayaran_cancelled_by'
        );

        $this->forge->createTable(
            'pembayaran',
            true
        );
    }

    public function down(): void
    {
        $this->forge->dropTable(
            'pembayaran',
            true
        );
    }
}