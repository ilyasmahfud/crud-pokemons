<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Coba extends Migration
{
    public function up()
    {
        $fieldColumn = [
            'id' => [
                'type' => 'int',
                'constraint' => 3,
                'unsigned' => true,
                'auto_increment' => true,
                // 'null' => false
            ],
            // 'number' => [
            //     'type' => 'int',
            //     'constraint' => 3,
            //     'unsigned' => true,
            //     'auto_increment' => true,
            //     // 'null' => false
            // ],
            'name' => [
                'type' => 'varchar',
                'null' => false
            ]
        ];

        $this->forge->addField($fieldColumn);
        $this->forge->addKey('id', true);
        $this->forge->createTable('coba', true);
    }

    public function down()
    {
        $this->forge->dropTable('coba');
    }
}
