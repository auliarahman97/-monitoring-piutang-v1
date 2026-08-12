<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePembayaranTable extends Migration
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

            /*
             * --------------------------------------------------------------
             * Relasi Piutang
             * --------------------------------------------------------------
             */
            'piutang_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],

            /*
             * --------------------------------------------------------------
             * Identitas Pembayaran
             * --------------------------------------------------------------
             */
            'nomor_pembayaran' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
            ],

            'tanggal_pembayaran' => [
                'type' => 'DATE',
            ],

            /*
             * --------------------------------------------------------------
             * Snapshot Tagihan Saat Pembayaran
             * --------------------------------------------------------------
             */
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

            /*
             * --------------------------------------------------------------
             * Alokasi Pembayaran
             *
             * Urutan bisnis:
             * Denda → Bunga → Pokok
             * --------------------------------------------------------------
             */
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

            /*
             * --------------------------------------------------------------
             * Snapshot Sisa Tagihan
             * --------------------------------------------------------------
             */
            'sisa_tagihan' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],

            /*
             * --------------------------------------------------------------
             * Status
             * --------------------------------------------------------------
             */
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'valid',
            ],

            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],

            /*
             * --------------------------------------------------------------
             * Audit User
             * --------------------------------------------------------------
             */
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

            /*
             * --------------------------------------------------------------
             * Timestamp
             * --------------------------------------------------------------
             */
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],

            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        /*
         * --------------------------------------------------------------
         * Primary Key
         * --------------------------------------------------------------
         */
        $this->forge->addKey('id', true);

        /*
         * --------------------------------------------------------------
         * Index
         * --------------------------------------------------------------
         */
        $this->forge->addKey('piutang_id');
        $this->forge->addKey('tanggal_pembayaran');
        $this->forge->addKey('status');

        /*
         * --------------------------------------------------------------
         * Unique Nomor Pembayaran
         * --------------------------------------------------------------
         */
        $this->forge->addUniqueKey('nomor_pembayaran');

        /*
         * --------------------------------------------------------------
         * Foreign Key
         * --------------------------------------------------------------
         *
         * Pembayaran tidak boleh ada tanpa Piutang.
         *
         * Piutang juga tidak boleh dihapus secara fisik apabila
         * sudah memiliki histori pembayaran.
         *
         */
        $this->forge->addForeignKey(
            'piutang_id',
            'piutang',
            'id',
            'CASCADE',
            'RESTRICT'
        );

        /*
         * --------------------------------------------------------------
         * Create Table
         * --------------------------------------------------------------
         */
        $this->forge->createTable(
            'pembayaran',
            true
        );
    }

    public function down()
    {
        /*
         * Hapus tabel pembayaran.
         *
         * Foreign key akan ikut dihapus bersama tabel.
         */
        $this->forge->dropTable(
            'pembayaran',
            true
        );
    }
}