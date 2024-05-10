<?php

namespace Tests\Feature\Tenant;

use Tests\TestCase;
use App\Models\Central\ExternalAccessToken;

class TenantTest extends TestCase
{
    protected $tenancy = true;

    protected $tenant = 'graphql';

    /**
     * A basic test route horizon for login.
     *
     * @test
     *
     * @dataProvider createTenantDataProvider
     *
     * @return void
     */
    public function createTenant(array $data, string $expectedMessage, int $expected_status)
    {
        $this->withHeaders([
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ]);

        if($data['token'] === false) {
            $data['token'] = 'test';
        } else {
            $data['token'] = ExternalAccessToken::first()->token;
        }

        $response = $this->postJson(
            $this->tenantUrl . '/v1/tenant',
            $data
        );

        $response->assertJson([
            'message' => trans($expectedMessage),
        ]);

        $response->assertStatus($expected_status);
    }

    public static function createTenantDataProvider()
    {
        return [
            'create tenant, success' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.messageSuccess',
                'expected_status' => 200,
            ],
            'create tenant, token incorrect, error' => [
                'data' => [
                    'token' => false,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'validation.token_invalid',
                'expected_status' => 422,
            ],
            'create tenant, validation email field is required, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.email.required',
                'expected_status' => 422,
            ],
            'create tenant, validation tenantId field is required, error' => [
                'data' => [
                    'token' => true,
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.tenantId.required',
                'expected_status' => 422,
            ],
            'create tenant, validation tenantId has already been taken, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'test',
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.tenantId.unique',
                'expected_status' => 422,
            ],
            'create tenant, validation email must be a valid email address, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'email' => 'tenant-test-' . rand(1, 1000),
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.email.email',
                'expected_status' => 422,
            ],
            'create tenant, validation tenantId must be a string, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 1,
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 'Tenant Test',
                ],
                'expected_message' => 'TenantCreate.tenantId.string',
                'expected_status' => 422,
            ],
            'create tenant, validation name field is required, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                ],
                'expected_message' => 'TenantCreate.name.required',
                'expected_status' => 422,
            ],
            'create tenant, validation name must be a string, error' => [
                'data' => [
                    'token' => true,
                    'tenantId' => 'tenant-test-' . rand(1, 1000),
                    'email' => 'tenant-test-' . rand(1, 1000) . '@test.com',
                    'name' => 1,
                ],
                'expected_message' => 'TenantCreate.name.string',
                'expected_status' => 422,
            ],
        ];
    }
}