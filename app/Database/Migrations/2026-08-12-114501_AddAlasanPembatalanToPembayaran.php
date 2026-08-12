<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAlasanPembatalanToPembayaran extends Migration
{
    public function up()
    {
        $this->forge->addColumn('pembayaran', [
            'alasan_pembatalan' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'keterangan',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn(
            'pembayaran',
            'alasan_pembatalan'
        );
    }
}