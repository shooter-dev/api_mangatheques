<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

final class LoginTest extends ApiTestCase
{
    public function testIfLoginReturnToken(): void
    {
        $response = static::createClient()->request(
            'GET',
            '/api/login_check',
            [
                'json' => [
                    'email' => 'user+1@email.com',
                    'password' => 'password',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        echo $response->toArray()['token'];
        $this->assertArrayHasKey('token', $response->toArray());
    }
}
