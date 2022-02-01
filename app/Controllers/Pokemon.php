<?php

namespace App\Controllers;

use App\Models\Pokemon as ModelsPokemon;
use App\Models\Type;
use App\Models\TypeTransaction;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;
use Michalsn\Uuid\Config\Uuid as ConfigUuid;
use Michalsn\Uuid\Uuid;

class Pokemon extends ResourceController
{
    use ResponseTrait;
    public $pokemon;
    public $type;
    public $typeTransaction;
    public $uuid;
    public $db;

    public function __construct()
    {
        $this->pokemon = new ModelsPokemon();
        $this->type = new Type();
        $this->typeTransaction = new TypeTransaction();
        $config = new ConfigUuid();
        $this->uuid = new Uuid($config);
        $this->db = \Config\Database::connect();
    }

    // GET ALL DATA
    public function index()
    {
        $data = $this->pokemon->findAll();

        if ($data != null) {
            for ($i = 0; $i < count($data); $i++) {
                $getId = $data[$i]['id'];

                // get type pokemons
                $dataTypeId = $this->typeTransaction->getWhere(['pokemon_id' => (string)$getId])->getResult();
                $typeAll = [];
                for ($j = 0; $j < count($dataTypeId); $j++) {
                    $type = $this->type->getWhere(['id' => (int)$dataTypeId[$j]->type_id])->getResult();
                    array_push($typeAll, (string) $type[0]->name);
                }

                $objekType = (object) ['type' => $typeAll];
                $data[$i] = array_merge($data[$i], (array) $objekType);
                // end get type pokemons
            }
        }

        $result = [
            'pokemons' => $data
        ];

        if ($data) {
            $response = [
                'data' => $result
            ];
            return $this->respond($response, 200);
        } else {
            return $this->respond($this->pokemon->errors());
        }
    }

    // GET DETAIL
    public function show($id = null)
    {
        $data = $this->pokemon->getWhere(['id' => $id])->getResult();
        if ($data == null) {
            return $this->failNotFound('No Data Found with id ' . $id);
        }

        // GET TYPE
        $dataTypeId = $this->typeTransaction->getWhere(['pokemon_id' => (string)$data[0]->id])->getResult();
        $typeAll = [];
        // STORE THE TYPE
        for ($j = 0; $j < count($dataTypeId); $j++) {
            $type = $this->type->getWhere(['id' => (int)$dataTypeId[$j]->type_id])->getResult();
            array_push($typeAll, (string) $type[0]->name);
        }

        // SHOW THE TYPE OF POKEMONS
        $objekType = (object) ['type' => $typeAll];
        $data[0] = array_merge((array)$data[0], (array) $objekType);

        $response = [
            'status' => 201,
            'error' => null,
            'messages' => [
                'success' => 'Succcesfully requested',
                'data' => (object)$data[0]
            ],
        ];

        return $this->respond($response, 200);
    }

    // CREATE DATA
    public function create()
    {
        // GET DATA FROM JSON INPUT
        $json = $this->request->getJSON();
        $data = [
            'id' => $this->uuid->uuid4(),
            'name' => $json->name,
            'type' => $json->type,
        ];

        // GET TYPE
        $arrayType = $data['type'];
        if ($arrayType != null) {
            for ($i = 0; $i < count($arrayType); $i++) {

                // CEK TYPE
                $cekType = $this->type->getWhere(['name' => (string) $arrayType[$i]])->getResult();

                // IF NOT EXIST THEN ADD TO NEW TYPE AND TRANSC TYPE
                if ($cekType != null) {
                    $dataType = [
                        'name' => (string) $arrayType[$i]
                    ];

                    $dataTypeTransaction = [
                        'pokemon_id' => $data['id'],
                        'type_id' => (int) $cekType[0]->id,
                    ];

                    $modelTypeTransaction = $this->typeTransaction->insert($dataTypeTransaction);

                    // IF EXIST THEN USE IT TO TRANSC TYPE
                } else if ($cekType == null) {
                    $dataType = [
                        'name' => (string) $arrayType[$i]
                    ];

                    $modelType = $this->type->insert($dataType);

                    $dataTypeTransaction = [
                        'pokemon_id' => $data['id'],
                        'type_id' => (int) $modelType,
                    ];

                    $modelTypeTransaction = $this->typeTransaction->insert($dataTypeTransaction);
                }
            }
        }

        // INSERT DATA
        $model = $this->pokemon->insert($data);

        if ($model) {
            $response = [
                'messages' => $data
            ];
            return $this->respond($response, 201);
        } else {
            return $this->respond($this->pokemon->errors());
        }
    }

    // UPDATE DATA
    public function update($id = null)
    {
        // CEK BY ID
        $cekId = $this->pokemon->getWhere(['id' => $id])->getResult();
        if ($cekId == null) {
            return $this->failNotFound('No Data Found with id ' . $id);
        }

        // return $this->respond($cekId, 200);

        // GET JSON DATA
        $json = $this->request->getJSON();
        $data = [
            'name' => $json->name,
            'type' => $json->type,
        ];

        // GET TYPE FROM INPUT JSON
        $arrayType = $data['type'];
        // return $this->respond($cekId, 200);
        if ($arrayType != null) {
            // COLECT THE ID OF TRANSC TYPE
            $arrayNewTypeOfPokemon = [];

            // UPDATE TYPE AND TRANSC TYPE POKEMONS
            for ($i = 0; $i < count($arrayType); $i++) {
                // CEK THE TYPE
                $cekType = $this->type->getWhere(['name' => (string) $arrayType[$i]])->getResult();
                // return $this->respond($cekType, 200);

                // IF TYPE IS EXISTS
                if ($cekType != null) {
                    // CEK IN TRANSC
                    $cekInTransaction = $this->typeTransaction->getWhere(['type_id' => (int) $cekType[0]->id, 'pokemon_id' => $cekId[0]->id])->getResult();

                    // IF EXIST, GET THE ID
                    if ($cekInTransaction != null) {
                        array_push($arrayNewTypeOfPokemon, (int) $cekInTransaction[0]->id);

                        // IF NOT EXIST THEN INSERT AS A NEW TYPE, THEN GET THE ID
                    } else if ($cekInTransaction == null) {
                        $dataTypeTransaction = [
                            'pokemon_id' => $id,
                            'type_id' => (int) $cekType[0]->id,
                        ];

                        $modelTypeTransaction = $this->typeTransaction->insert($dataTypeTransaction);

                        array_push($arrayNewTypeOfPokemon, $modelTypeTransaction);
                    }

                    // ID TYPE NOT EXIST ADD AS NEW TYPE AND TRANSC TYPE
                } else if ($cekType == null) {
                    $dataType = [
                        'name' => (string) $arrayType[$i]
                    ];

                    $modelType = $this->type->insert($dataType);

                    $dataTypeTransaction = [
                        'pokemon_id' => $id,
                        'type_id' => (int) $modelType,
                    ];

                    $modelTypeTransaction = $this->typeTransaction->insert($dataTypeTransaction);
                }
            }

            // UPDATE THE TYPE BY DELETEING THE TYPE THAT NOT EXISTS IN JSON INPUT
            $sql = $this->db->table('pokemonTypeTransaction');
            $sql->where('pokemon_id', $id);
            $sql->whereNotIn('id', $arrayNewTypeOfPokemon);
            $sql->delete();
        }



        // UPDATE NAME IN TABLE POKEMONS
        $updatePokemon = [
            'name' => $data['name']
        ];

        $sql = $this->db->table('pokemons'); //update('pokemons', $data, ['id' => $id]);
        $sql->update($updatePokemon, ['id' => $id]);

        $getDAta = $this->show($id);

        if ($sql) {
            $response = [
                'status' => 201,
                'error' => null,
                'messages' => [
                    'success' => 'Succcesfully updated',
                    'pokemon' => $updatePokemon['name']
                ]
            ];
            return $this->respond($response, 201);
        } else {
            return $this->respond($this->pokemon->errors());
        }
    }

    public function try()
    {
        return $this->index();
    }
}
