<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixAuditForeignKeys extends Migration
{
    public function up()
    {
        /*
         * Audit trail harus bersifat optional terhadap user.
         *
         * Jika user dihapus:
         * - data bisnis tetap ada
         * - field audit menjadi NULL
         *
         * Data transaksi tidak boleh ikut terhapus karena
         * user yang membuat/mengubah data tersebut dihapus.
         */

        // =========================================================
        // CUSTOMER
        // =========================================================

        $this->forge->dropForeignKey('customer', 'customer_created_by_foreign');
        $this->forge->dropForeignKey('customer', 'customer_updated_by_foreign');
        $this->forge->dropForeignKey('customer', 'customer_deleted_by_foreign');

        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'customer_created_by_foreign'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'customer_updated_by_foreign'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'SET NULL',
            'RESTRICT',
            'customer_deleted_by_foreign'
        );
    }

    public function down()
    {
        // Kembalikan perilaku FK sebelumnya apabila rollback diperlukan.

        $this->forge->dropForeignKey('customer', 'customer_created_by_foreign');
        $this->forge->dropForeignKey('customer', 'customer_updated_by_foreign');
        $this->forge->dropForeignKey('customer', 'customer_deleted_by_foreign');

        $this->forge->addForeignKey(
            'created_by',
            'users',
            'id',
            'CASCADE',
            'RESTRICT',
            'customer_created_by_foreign'
        );

        $this->forge->addForeignKey(
            'updated_by',
            'users',
            'id',
            'CASCADE',
            'RESTRICT',
            'customer_updated_by_foreign'
        );

        $this->forge->addForeignKey(
            'deleted_by',
            'users',
            'id',
            'CASCADE',
            'RESTRICT',
            'customer_deleted_by_foreign'
        );
    }
}