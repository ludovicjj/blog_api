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
//        $this->login($client, 'john@example.com', 'foo');
    }
}