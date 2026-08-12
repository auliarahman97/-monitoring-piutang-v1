<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePiutangTableFinal extends Migration
{
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Fields
        |--------------------------------------------------------------------------
        */

        $this->forge->addField([

            // --------------------------------------------------------------
            // Primary Key
            // --------------------------------------------------------------

            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],


            // --------------------------------------------------------------
            // Customer
            // --------------------------------------------------------------

            'customer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],


            // --------------------------------------------------------------
            // Identitas Piutang
            // --------------------------------------------------------------

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


            // --------------------------------------------------------------
            // Nilai Pokok
            // --------------------------------------------------------------

            'nominal_pokok' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0,
            ],


            // --------------------------------------------------------------
            // Bunga
            // --------------------------------------------------------------

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


            // --------------------------------------------------------------
            // Versi Aturan Denda
            // --------------------------------------------------------------
            //
            // Piutang terikat secara permanen kepada versi aturan denda
            // yang berlaku ketika piutang dibuat.
            //
            // Tidak nullable karena setiap piutang wajib memiliki
            // referensi versi denda.
            //

            'denda_versi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],


            // --------------------------------------------------------------
            // Snapshot Aturan Denda
            // --------------------------------------------------------------
            //
            // Nilai ini disalin ketika piutang dibuat.
            //
            // Tujuannya agar perubahan aturan denda di masa depan
            // tidak mengubah histori piutang lama.
            //

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


            // --------------------------------------------------------------
            // Keterangan
            // --------------------------------------------------------------

            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],


            // --------------------------------------------------------------
            // Audit Trail
            // --------------------------------------------------------------

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


            // --------------------------------------------------------------
            // Timestamp
            // --------------------------------------------------------------

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


        /*
        |--------------------------------------------------------------------------
        | Primary Key
        |--------------------------------------------------------------------------
        */

        $this->forge->addKey(
            'id',
            true
        );


        /*
        |--------------------------------------------------------------------------
        | Index
        |--------------------------------------------------------------------------
        */

        $this->forge->addKey(
            'customer_id'
        );

        $this->forge->addKey(
            'denda_versi_id'
        );

        $this->forge->addKey(
            'tanggal_piutang'
        );

        $this->forge->addKey(
            'tanggal_jatuh_tempo'
        );


        /*
        |--------------------------------------------------------------------------
        | Unique Nomor Piutang
        |--------------------------------------------------------------------------
        |
        | Nomor piutang harus unik.
        |
        */

        $this->forge->addUniqueKey(
            'nomor_piutang'
        );


        /*
        |--------------------------------------------------------------------------
        | Foreign Key Customer
        |--------------------------------------------------------------------------
        |
        | RESTRICT digunakan agar customer yang masih memiliki
        | histori piutang tidak dapat dihapus secara sembarangan.
        |
        */

        $this->forge->addForeignKey(
            'customer_id',
            'customer',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        /*
        |--------------------------------------------------------------------------
        | Foreign Key Versi Denda
        |--------------------------------------------------------------------------
        */

        $this->forge->addForeignKey(
            'denda_versi_id',
            'aturan_denda_versi',
            'id',
            'RESTRICT',
            'RESTRICT'
        );


        /*
        |--------------------------------------------------------------------------
        | Create Table
        |--------------------------------------------------------------------------
        */

        $this->forge->createTable(
            'piutang'
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