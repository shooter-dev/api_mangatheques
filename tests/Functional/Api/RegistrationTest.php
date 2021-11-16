<?php

declare(strict_types=1);

namespace App\Tests\Functional\Api;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\User;

final class RegistrationTest extends ApiTestCase
{
    public function testIfUserIsRegistered(): void
    {
        static::createClient()->request(
            'POST',
            '/api/users',
            [
                'json' => [
                    'email' => 'user+2@email.com',
                    'password' => 'password',
                    'firstName' => 'John',
                    'lastName' => 'Doe',
                ],
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertMatchesResourceItemJsonSchema(User::class);
    }
}
