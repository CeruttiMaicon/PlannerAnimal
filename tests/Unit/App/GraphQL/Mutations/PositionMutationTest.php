<?php

namespace Tests\Unit\App\GraphQL\Mutations;

use App\GraphQL\Mutations\PositionMutation;
use App\Models\Position;
use Mockery\MockInterface;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;
use Tests\TestCase;

class PositionMutationTest extends TestCase
{
    /**
     * A basic unit test create and edit position.
     *
     * @dataProvider positionProvider
     *
     * @return void
     */
    public function test_position_make($data, $method)
    {
        $graphQLContext = $this->createMock(GraphQLContext::class);
        $positionMock = $this->mock(Position::class, function (MockInterface $mock) use ($data, $method) {
            if ($data['id']) {
                $mock->shouldReceive('find')
                    ->once()
                    ->with($data['id'])
                    ->andReturn($mock);
            }

            $mock->shouldReceive($method)->with($data)->once()->andReturn($mock);
        });

        $specificFundamentalMutation = new PositionMutation($positionMock);
        $positionMockReturn = $specificFundamentalMutation->make(
            null,
            $data,
            $graphQLContext
        );

        $this->assertEquals($positionMock, $positionMockReturn);
    }

    public function positionProvider()
    {
        return [
            'send data create, success' => [
                'data' => [
                    'id' => null,
                    'name' => 'Teste',
                    'user_id' => 1,
                ],
                'method' => 'create',
            ],
            'send data edit, success' => [
                'data' => [
                    'id' => 1,
                    'name' => 'Teste',
                    'user_id' => 1,
                ],
                'method' => 'update',
            ],
        ];
    }

    /**
     * A basic unit test in delete position.
     *
     * @dataProvider positionDeleteProvider
     *
     * @return void
     */
    public function test_position_delete($data, $number)
    {
        $graphQLContext = $this->createMock(GraphQLContext::class);
        $position = $this->createMock(Position::class);

        $position->expects($this->exactly($number))
            ->method('deletePosition')
            ->willReturn($position);

        $positionMutation = new PositionMutation($position);
        $positionMutation->delete(
            null,
            [
                'id' => $data,
            ],
            $graphQLContext
        );
    }

    public function positionDeleteProvider()
    {
        return [
            'send array, success' => [
                [1],
                1,
            ],
            'send multiple itens in array, success' => [
                [1, 2, 3],
                3,
            ],
            'send empty array, success' => [
                [],
                0,
            ],
        ];
    }
}
