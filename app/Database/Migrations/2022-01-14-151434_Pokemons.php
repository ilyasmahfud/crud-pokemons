<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Pokemons extends Migration
{
    public function up()
    {
        $fieldColumn = [
            'id' => [
                'type' => 'VARCHAR',
                'null' => false,
                'unique' => true,
            ],
            'number' => [
                'type' => 'INT',
                'constraint' => 3,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false
            ]
        ];

        $this->forge->addField($fieldColumn);
        $this->forge->addKey('number', true);
        $this->forge->createTable('pokemons', true);
    }

    public function down()
    {
        $this->forge->dropTable('pokemons');
    }
}
