<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * --------------------------------------------------------------------------
 * Create Customer Table
 * --------------------------------------------------------------------------
 *
 * Master data customer untuk Sistem Monitoring Piutang.
 */
class CreateCustomerTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            // =====================================================
            // Primary Key
            // =====================================================
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],

            // =====================================================
            // Business Fields
            // =====================================================

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

            // =====================================================
            // Audit Trail
            // =====================================================

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

            // =====================================================
            // Timestamp
            // =====================================================

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

        // =====================================================
        // Primary Key
        // =====================================================

        $this->forge->addKey('id', true);

        // =====================================================
        // Index
        // =====================================================

        $this->forge->addUniqueKey('kode_customer');

        $this->forge->addKey('nama');

        $this->forge->addKey('status');

        // no_hp tidak saya index dulu karena belum tentu sering
        // dipakai untuk pencarian. Jika nanti diperlukan, kita
        // bisa menambahkannya tanpa mengubah struktur tabel.

        // =====================================================
        // Foreign Key
        // =====================================================

        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'RESTRICT',
            'CASCADE'
        );

        // =====================================================
        // Create Table
        // =====================================================

        $this->forge->createTable('customer');
    }

    public function down(): void
    {
        $this->forge->dropTable('customer', true);
    }
}