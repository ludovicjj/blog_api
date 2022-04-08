<?php

namespace App\Tests\functional;

use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use RecreateDatabaseTrait;

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
        $user = $this->refreshEntity(User::class, ['email' => 'john@example.com']);
        $this->assertNotNull($user);

        $this->assertJsonContains([
            '@id' => '/api/users/'. $user->getUuid()->toString(),
            'isMe' => false
        ]);

        $this->login($client, 'john@example.com', 'foo');
    }

    public function testUpdateUser()
    {
        $client = static::createClient();
        $user = $this->createUserAndLogin($client, 'john@example.com', 'foo');
        $client->request('PUT', '/api/users/'.$user->getUuid()->toString(), [
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
        $user = $this->refreshEntity(User::class, ['email' => 'john@example.com']);
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetUser()
    {
        $client = static::createClient();
        $this->loadFixtures(['tests/fixtures/user/get_item_user.yaml']);

        $this->login($client, 'user2@example.com', 'foo');

        /** @var User $user1 */
        $user1 = $this->refreshEntity(User::class, ['email' => 'user1@example.com']);
        $response = $client->request('GET', '/api/users/'.$user1->getUuid()->toString());
        $this->assertJsonContains([
            'username' => 'user1',
            'isMe' => false,
            'isMvp' => false
        ]);
        $data = $response->toArray();
        $this->assertArrayNotHasKey('phoneNumber', $data);

        $this->login($client, 'user1@example.com', 'foo');
        $client->request('GET', '/api/users/'.$user1->getUuid()->toString());
        $this->assertJsonContains([
            'username' => 'user1',
            'phoneNumber' => '0123456789',
            'isMe' => true,
            'isMvp' => false
        ]);

        // refresh user because entity manager don't remember it handle him
        // and update his role
        $user2 = $this->refreshEntity(User::class, ['email' => 'user2@example.com']);
        $user2->setRoles(['ROLE_ADMIN']);
        $this->persist();

        // Re login to update role in security component
        // And test if admin user can read phone number in response
        $this->login($client, 'user2@example.com', 'foo');
        $client->request('GET', '/api/users/'.$user1->getUuid()->toString());
        $this->assertJsonContains([
            'username' => 'user1',
            'phoneNumber' => '0123456789',
            'isMe' => false,
            'isMvp' => false
        ]);

        // user is MVP if their username contains the word "cheese"
        /** @var User $user3 */
        $user3 = $this->refreshEntity(User::class, ['email' => 'cheesehead@example.com']);
        $client->request('GET', '/api/users/'.$user3->getUuid()->toString());
        $this->assertJsonContains([
            'username' => 'cheesehead',
            'isMe' => false,
            'isMvp' => true
        ]);
    }
}