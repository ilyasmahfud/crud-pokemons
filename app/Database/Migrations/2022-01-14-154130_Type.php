<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Type extends Migration
{
    public function up()
    {
        $fieldColumn = [
            'id' => [
                'type' => 'int',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'varchar',
                'null' => false
            ]
        ];

        $this->forge->addField($fieldColumn);
        $this->forge->addKey('id', true);
        $this->forge->createTable('types', true);
    }

    public function down()
    {
        $this->forge->dropTable('types');
    }
}
