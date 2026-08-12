<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVersiIdToAturanDenda extends Migration
{
    public function up()
    {
        $this->forge->addColumn('aturan_denda', [
            'versi_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'id',
            ],
        ]);

        $this->forge->addKey(
            'versi_id',
            false,
            'idx_aturan_denda_versi_id'
        );
    }

    public function down()
    {
        $this->forge->dropColumn(
            'aturan_denda',
            'versi_id'
        );
    }
}