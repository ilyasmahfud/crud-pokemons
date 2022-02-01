<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PokemonTypeTransaction extends Migration
{
    public function up()
    {
        $fieldColumn = [
            'id' => [
                'type' => 'int',
                'constraint' => 3,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'pokemon_id' => [
                'type' => 'varchar',
                'null' => false
            ],
            'type_id' => [
                'type' => 'int',
                'null' => false
            ]
        ];

        $this->forge->addField($fieldColumn);
        // $this->forge->addForeignKey('pokemon_id', 'pokemons', 'id');
        // $this->forge->addForeignKey('type_id', 'types', 'id');
        $this->forge->addKey('id', true);
        $this->forge->createTable('pokemonTypeTransaction', true);
    }

    public function down()
    {
        $this->forge->dropTable('pokemonTypeTransaction');
    }
}
