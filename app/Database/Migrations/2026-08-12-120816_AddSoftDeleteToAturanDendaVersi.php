<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToAturanDendaVersi extends Migration
{
    public function up()
    {
        $fields = [];

        $existingFields = $this->db
            ->getFieldNames('aturan_denda_versi');

        /*
         * Soft delete timestamp.
         */
        if (! in_array('deleted_at', $existingFields, true)) {
            $fields['deleted_at'] = [
                'type' => 'DATETIME',
                'null' => true,
            ];
        }

        /*
         * Audit user yang melakukan delete.
         */
        if (! in_array('deleted_by', $existingFields, true)) {
            $fields['deleted_by'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn(
                'aturan_denda_versi',
                $fields
            );
        }
    }

    public function down()
    {
        $existingFields = $this->db
            ->getFieldNames('aturan_denda_versi');

        $dropFields = [];

        if (in_array('deleted_at', $existingFields, true)) {
            $dropFields[] = 'deleted_at';
        }

        if (in_array('deleted_by', $existingFields, true)) {
            $dropFields[] = 'deleted_by';
        }

        if (! empty($dropFields)) {
            $this->forge->dropColumn(
                'aturan_denda_versi',
                $dropFields
            );
        }
    }
}