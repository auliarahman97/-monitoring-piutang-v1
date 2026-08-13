<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCustomerTable extends Migration
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

            'kode_customer' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
            ],

            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],

            'nik' => [
                'type'       => 'VARCHAR',
                'constraint' => 16,
                'null'       => true,
            ],

            'no_hp' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],

            'alamat' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            'tanggal_terdaftar' => [
                'type' => 'DATE',
            ],

            'status' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = Aktif, 0 = Tidak Aktif',
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
            'kode_customer',
            'uq_customer_kode'
        );

        // Index
        $this->forge->addKey(
            'nama',
            false,
            false,
            'idx_customer_nama'
        );

        $this->forge->addKey(
            'status',
            false,
            false,
            'idx_customer_status'
        );

        // Audit foreign keys
        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_customer_created_by'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_customer_updated_by'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'fk_customer_deleted_by'
        );

        $this->forge->createTable('customer', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('customer', true);
    }
}