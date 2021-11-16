<?php

declare(strict_types=1);

namespace App\Tests\Functional\Admin;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class LoginTest extends WebTestCase
{
    public function testIfAdministratorIsLogged(): void
    {
        $client = static::createClient();

        $client->request('GET', '/admin');

        $this->assertResponseRedirects();

        $client->followRedirect();

        $client->submitForm('Se connecter', [
            '_username' => 'admin+1@email.com',
            '_password' => 'password',
        ]);

        $this->assertResponseRedirects();

        $client->followRedirect();

        $this->assertRouteSame('admin_dashboard');

    }
}