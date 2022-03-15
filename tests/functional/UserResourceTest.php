<?php

namespace App\Tests\functional;

use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser(): void
    {
        $client = static::createClient();
        $client->request('POST', '/api/users', [
            'json' => [
                'email' => 'john@example.com',
                'password' => 'foo',
                'username' => 'john'
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);
        $this->login($client, 'john@example.com', 'foo');
    }

    public function testUpdateUser()
    {
        $client = static::createClient();
        $user = $this->createUserAndLogin($client, 'john@example.com', 'foo');
        $client->request('PUT', '/api/users/'.$user->getId(), [
            'json' => [
                'username' => 'newusername'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => 'newusername'
        ]);
    }
}