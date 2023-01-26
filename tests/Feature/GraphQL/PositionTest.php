<?php

namespace Tests\Feature\GraphQL;

use App\Models\Position;
use Faker\Factory as Faker;
use Tests\TestCase;

class PositionTest extends TestCase
{
    protected $graphql = true;

    protected $tenancy = true;

    protected $login = true;

    private $role = 'technician';

    private $data = [
        'id',
        'name',
        'userId',
        'createdAt',
        'updatedAt',
    ];

    private function setPermissions(bool $hasPermission)
    {
        $this->checkPermission($hasPermission, $this->role, 'edit-position');
        $this->checkPermission($hasPermission, $this->role, 'view-position');
    }

    /**
     * Listagem de todos os fundamentos.
     *
     * @author Maicon Cerutti
     *
     * @test
     *
     * @dataProvider listProvider
     *
     * @return void
     */
    public function positionsList(
        $typeMessageError,
        $expectedMessage,
        $expected,
        bool $hasPermission
    ) {
        $this->setPermissions($hasPermission);

        Position::factory()->make()->save();

        $response = $this->graphQL(
            'positions',
            [
                'name' => '%%',
                'first' => 10,
                'page' => 1,
            ],
            [
                'paginatorInfo' => $this->paginatorInfo,
                'data' => $this->data,
            ],
            'query',
            false
        );

        $this->assertMessageError(
            $typeMessageError,
            $response,
            $hasPermission,
            $expectedMessage
        );

        if ($hasPermission) {
            $response
                ->assertJsonStructure($expected)
                ->assertStatus(200);
        }
    }

    /**
     * @return array
     */
    public function listProvider()
    {
        return [
            'with permission' => [
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'data' => [
                        'positions' => [
                            'paginatorInfo' => $this->paginatorInfo,
                            'data' => [
                                '*' => $this->data,
                            ],
                        ],
                    ],
                ],
                'hasPermission' => true,
            ],
            'without permission' => [
                'type_message_error' => 'message',
                'expected_message' => $this->unauthorized,
                'expected' => [
                    'errors' => $this->errors,
                ],
                'hasPermission' => false,
            ],
        ];
    }

    /**
     * Listagem de um fundamento
     *
     * @author Maicon Cerutti
     *
     * @test
     *
     * @dataProvider infoProvider
     *
     * @return void
     */
    public function positionInfo(
        $typeMessageError,
        $expectedMessage,
        $expected,
        bool $hasPermission
    ) {
        $this->setPermissions($hasPermission);

        $position = Position::factory()->make();
        $position->save();

        $response = $this->graphQL(
            'position',
            [
                'id' => $position->id,
            ],
            $this->data,
            'query',
            false
        );

        $this->assertMessageError(
            $typeMessageError,
            $response,
            $hasPermission,
            $expectedMessage
        );

        if ($hasPermission) {
            $response
                ->assertJsonStructure($expected)
                ->assertStatus(200);
        }
    }

    /**
     * @return array
     */
    public function infoProvider()
    {
        return [
            'with permission' => [
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'data' => [
                        'position' => $this->data,
                    ],
                ],
                'hasPermission' => true,
            ],
            'without permission' => [
                'type_message_error' => 'message',
                'expected_message' => $this->unauthorized,
                'expected' => [
                    'errors' => $this->errors,
                ],
                'hasPermission' => false,
            ],
        ];
    }

    /**
     * Método de criação de um fundamento.
     *
     * @dataProvider positionCreateProvider
     *
     * @author Maicon Cerutti
     *
     * @test
     *
     * @return void
     */
    public function positionCreate(
        $parameters,
        $typeMessageError,
        $expectedMessage,
        $expected,
        bool $hasPermission
        ) {
        $this->checkPermission($hasPermission, $this->role, 'edit-position');

        $response = $this->graphQL(
            'positionCreate',
            $parameters,
            $this->data,
            'mutation',
            false,
            true
        );

        $this->assertMessageError($typeMessageError, $response, $hasPermission, $expectedMessage);

        $response
            ->assertJsonStructure($expected)
            ->assertStatus(200);
    }

    /**
     * @return array
     */
    public function positionCreateProvider()
    {
        $faker = Faker::create();
        $userId = 1;
        $nameExistent = $faker->name;
        $positionCreate = ['positionCreate'];

        return [
            'create position, success' => [
                [
                    'name' => $nameExistent,
                    'userId' => $userId,
                ],
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'data' => [
                        'positionCreate' => $this->data,
                    ],
                ],
                'hasPermission' => true,
            ],
            'create position without permission, expected error' => [
                [
                    'name' => $faker->name,
                    'userId' => $userId,
                ],
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionCreate,
                ],
                'hasPermission' => false,
            ],
            'name field is not unique, expected error' => [
                [
                    'name' => $nameExistent,
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionCreate.name_unique',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionCreate,
                ],
                'hasPermission' => true,
            ],
            'name field is required, expected error' => [
                [
                    'name' => ' ',
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionCreate.name_required',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionCreate,
                ],
                'hasPermission' => true,
            ],
            'name field is min 3 characteres, expected error' => [
                [
                    'name' => 'AB',
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionCreate.name_min',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionCreate,
                ],
                'hasPermission' => true,
            ],
        ];
    }

    /**
     * Método de edição de um fundamento.
     *
     * @dataProvider positionEditProvider
     *
     * @test
     *
     * @author Maicon Cerutti
     *
     * @return void
     */
    public function positionEdit(
        $parameters,
        $typeMessageError,
        $expectedMessage,
        $expected,
        bool $hasPermission
        ) {
        $this->checkPermission($hasPermission, $this->role, 'edit-position');

        $positionExist = Position::factory()->make();
        $positionExist->save();
        $position = Position::factory()->make();
        $position->save();

        $parameters['id'] = $position->id;

        if ($expectedMessage == 'PositionEdit.name_unique') {
            $parameters['name'] = $positionExist->name;
        }

        $response = $this->graphQL(
            'positionEdit',
            $parameters,
            $this->data,
            'mutation',
            false,
            true
        );

        $this->assertMessageError($typeMessageError, $response, $hasPermission, $expectedMessage);

        $response
            ->assertJsonStructure($expected)
            ->assertStatus(200);
    }

    /**
     * @return array
     */
    public function positionEditProvider()
    {
        $faker = Faker::create();
        $userId = 2;
        $positionEdit = ['positionEdit'];

        return [
            'edit position without permission, expected error' => [
                [
                    'name' => $faker->name,
                    'userId' => $userId,
                ],
                'type_message_error' => 'message',
                'expected_message' => $this->unauthorized,
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionEdit,
                ],
                'hasPermission' => false,
            ],
            'edit position, success' => [
                [
                    'name' => $faker->name,
                    'userId' => $userId,
                ],
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'data' => [
                        'positionEdit' => $this->data,
                    ],
                ],
                'hasPermission' => true,
            ],
            'name field is not unique, expected error' => [
                [
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionEdit.name_unique',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionEdit,
                ],
                'hasPermission' => true,
            ],
            'name field is required, expected error' => [
                [
                    'name' => ' ',
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionEdit.name_required',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionEdit,
                ],
                'hasPermission' => true,
            ],
            'name field is min 3 characteres, expected error' => [
                [
                    'name' => 'AB',
                    'userId' => $userId,
                ],
                'type_message_error' => 'name',
                'expected_message' => 'PositionEdit.name_min',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionEdit,
                ],
                'hasPermission' => true,
            ],
        ];
    }

    /**
     * Método de exclusão de uma posição.
     *
     * @author Maicon Cerutti
     *
     * @test
     *
     * @dataProvider positionDeleteProvider
     *
     * @return void
     */
    public function positionDelete(
        $data,
        $typeMessageError,
        $expectedMessage,
        $expected,
        bool $hasPermission
    ) {
        $this->login = true;

        $this->checkPermission($hasPermission, $this->role, 'edit-position');

        $position = Position::factory()->make();
        $position->save();

        $parameters['id'] = $position->id;

        if ($data['error'] != null) {
            $parameters['id'] = $data['error'];
        }

        $response = $this->graphQL(
            'positionDelete',
            $parameters,
            $this->data,
            'mutation',
            false,
            true
        );

        $this->assertMessageError(
            $typeMessageError,
            $response,
            $hasPermission,
            $expectedMessage
        );

        $response
            ->assertJsonStructure($expected)
            ->assertStatus(200);
    }

    /**
     * @author Maicon Cerutti
     *
     * @return array
     */
    public function positionDeleteProvider()
    {
        $positionDelete = ['positionDelete'];

        return [
            'delete position, success' => [
                [
                    'error' => null,
                ],
                'type_message_error' => false,
                'expected_message' => false,
                'expected' => [
                    'data' => [
                        'positionDelete' => [$this->data],
                    ],
                ],
                'hasPermission' => true,
            ],
            'delete position without permission, expected error' => [
                [
                    'error' => null,
                ],
                'type_message_error' => 'message',
                'expected_message' => $this->unauthorized,
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionDelete,
                ],
                'hasPermission' => false,
            ],
            'delete position that does not exist, expected error' => [
                [
                    'error' => 9999,
                ],
                'type_message_error' => 'message',
                'expected_message' => 'internal',
                'expected' => [
                    'errors' => $this->errors,
                    'data' => $positionDelete,
                ],
                'hasPermission' => true,
            ],
        ];
    }
}
