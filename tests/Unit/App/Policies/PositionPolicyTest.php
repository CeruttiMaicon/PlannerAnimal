<?php

namespace Tests\Unit\App\Policies;

use App\Models\User;
use App\Policies\PositionPolicy;
use Tests\TestCase;

class PositionPolicyTest extends TestCase
{
    /**
     * A basic unit test create.
     *
     * @dataProvider createProvider
     *
     * @return void
     */
    public function test_create(bool $expected): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('create-position')
            ->willReturn($expected);

        $positionPolicy = new PositionPolicy();
        $positionPolicy->create($user);
    }

    public function createProvider(): array
    {
        return [
            'when permission allows' => [
                true,
            ],
            'when permission does not allow' => [
                false
            ],
        ];
    }

    /**
     * A basic unit test edit.
     *
     * @dataProvider editProvider
     *
     * @return void
     */
    public function test_edit(bool $expected): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('edit-position')
            ->willReturn($expected);

        $positionPolicy = new PositionPolicy();
        $positionPolicy->edit($user);
    }

    public function editProvider(): array
    {
        return [
            'when permission allows' => [
                true,
            ],
            'when permission does not allow' => [
                false
            ],
        ];
    }

    /**
     * A basic unit test delete.
     *
     * @dataProvider deleteProvider
     *
     * @return void
     */
    public function test_delete(bool $expected): void
    {
        $user = $this->createMock(User::class);
        $user->expects($this->once())
            ->method('hasPermissionTo')
            ->with('delete-position')
            ->willReturn($expected);

        $positionPolicy = new PositionPolicy();
        $positionPolicy->delete($user);
    }

    public function deleteProvider(): array
    {
        return [
            'when permission allows' => [
                true,
            ],
            'when permission does not allow' => [
                false
            ],
        ];
    }
}
