<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RepairDatabaseForeignKeys extends Migration
{
    public function up()
    {
        /*
         * Final repair of foreign key constraints.
         *
         * Existing indexes are intentionally preserved.
         * Constraint names are deliberately different from
         * existing index names to avoid MySQL errno 121.
         */

        // =========================================================
        // CUSTOMER → USERS
        // =========================================================

        $this->db->query("
            ALTER TABLE customer
            ADD CONSTRAINT rel_customer_created_by_users
            FOREIGN KEY (created_by)
            REFERENCES users(id)
            ON UPDATE RESTRICT
            ON DELETE SET NULL
        ");

        $this->db->query("
            ALTER TABLE customer
            ADD CONSTRAINT rel_customer_updated_by_users
            FOREIGN KEY (updated_by)
            REFERENCES users(id)
            ON UPDATE RESTRICT
            ON DELETE SET NULL
        ");

        $this->db->query("
            ALTER TABLE customer
            ADD CONSTRAINT rel_customer_deleted_by_users
            FOREIGN KEY (deleted_by)
            REFERENCES users(id)
            ON UPDATE RESTRICT
            ON DELETE SET NULL
        ");

        // =========================================================
        // PIUTANG → CUSTOMER
        // =========================================================

        $this->db->query("
            ALTER TABLE piutang
            ADD CONSTRAINT rel_piutang_customer
            FOREIGN KEY (customer_id)
            REFERENCES customer(id)
            ON UPDATE RESTRICT
            ON DELETE RESTRICT
        ");

        // =========================================================
        // PIUTANG → ATURAN DENDA VERSI
        // =========================================================

        $this->db->query("
            ALTER TABLE piutang
            ADD CONSTRAINT rel_piutang_denda_versi
            FOREIGN KEY (denda_versi_id)
            REFERENCES aturan_denda_versi(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        ");

        // =========================================================
        // PEMBAYARAN → PIUTANG
        // =========================================================

        $this->db->query("
            ALTER TABLE pembayaran
            ADD CONSTRAINT rel_pembayaran_piutang
            FOREIGN KEY (piutang_id)
            REFERENCES piutang(id)
            ON UPDATE RESTRICT
            ON DELETE RESTRICT
        ");

        // =========================================================
        // ATURAN DENDA → ATURAN DENDA VERSI
        // =========================================================

        $this->db->query("
            ALTER TABLE aturan_denda
            ADD CONSTRAINT rel_aturan_denda_versi
            FOREIGN KEY (versi_id)
            REFERENCES aturan_denda_versi(id)
            ON UPDATE CASCADE
            ON DELETE RESTRICT
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE customer
            DROP FOREIGN KEY rel_customer_created_by_users
        ");

        $this->db->query("
            ALTER TABLE customer
            DROP FOREIGN KEY rel_customer_updated_by_users
        ");

        $this->db->query("
            ALTER TABLE customer
            DROP FOREIGN KEY rel_customer_deleted_by_users
        ");

        $this->db->query("
            ALTER TABLE piutang
            DROP FOREIGN KEY rel_piutang_customer
        ");

        $this->db->query("
            ALTER TABLE piutang
            DROP FOREIGN KEY rel_piutang_denda_versi
        ");

        $this->db->query("
            ALTER TABLE pembayaran
            DROP FOREIGN KEY rel_pembayaran_piutang
        ");

        $this->db->query("
            ALTER TABLE aturan_denda
            DROP FOREIGN KEY rel_aturan_denda_versi
        ");
    }
}