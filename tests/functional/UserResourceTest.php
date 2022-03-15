<?php

namespace App\Tests\functional;

use App\Entity\User;
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
                'username' => 'newusername',
                'roles' => ['ROLE_ADMIN']
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'username' => 'newusername'
        ]);
        /** @var User $user */
        $user = $this->getEntityManager()->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = static::createClient();
        $user = $this->createUserAndLogin($client, 'test@example.com', 'foo');
        $user->setPhoneNumber('0199999999');
        $em = $this->getEntityManager();
        $em->flush();

        $response = $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains([
            'username' => 'test'
        ]);
        $data = $response->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        // refresh user because entity manager don't remember it handle him
        // and update his role
        $user = $em->getRepository(User::class)->findOneBy(['id' => $user->getId()]);
        $user->setRoles(['ROLE_ADMIN']);
        $em->flush();

        // Re login to update role in security component
        // And test if admin user can read phone number in response
        $this->login($client, 'test@example.com', 'foo');
        $client->request('GET', '/api/users/'.$user->getId());
        $this->assertJsonContains([
            'username' => 'test',
            'phoneNumber' => '0199999999'
        ]);
    }
}