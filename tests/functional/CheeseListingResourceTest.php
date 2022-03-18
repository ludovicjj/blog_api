<?php

namespace App\Tests\functional;

use App\Entity\CheeseListing;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();
        $client->request("POST", "/api/cheeses", [
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401);

        $authenticatedUser = $this->createUserAndLogin($client, 'authenticated@example.com', 'foo');
        $otherUser = $this->createUser('otheruser@example.com', 'foo');

        $client->request('POST', '/api/cheeses', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(422);

        $cheeseData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000
        ];

        $client->request('POST', '/api/cheeses', [
            'json' => $cheeseData,
        ]);
        $this->assertResponseStatusCodeSame(201);

        $client->request("POST", "/api/cheeses", [
            'json' => $cheeseData + ['owner' => '/api/users/'. $otherUser->getId()]
        ]);
        $this->assertResponseStatusCodeSame(422, 'not passing the correct owner');

        $client->request("POST", "/api/cheeses", [
            'json' => $cheeseData + ['owner' => '/api/users/'. $authenticatedUser->getId()]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $user = $this->createUser('test@example.com', 'foo');

        $this->createCheeseListing('cheese1', 'cheese', 100, $user);
        $this->createCheeseListing('cheese2', 'cheese', 100, $user, true);
        $this->createCheeseListing('cheese3', 'cheese', 100, $user, true);

        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $user = $this->createUser('user@example.com', 'foo');
        $cheese = $this->createCheeseListing('cheese1', 'cheese', 100, $user);
        $client->request('GET', '/api/cheeses/'.$cheese->getId());
        $this->assertResponseStatusCodeSame(404);

        $admin = $this->createUser('admin@example.com', 'foo', ['ROLE_ADMIN']);
        $this->login($client, 'admin@example.com', 'foo');
        $client->request('GET', '/api/cheeses/'.$cheese->getId());
        $this->assertResponseStatusCodeSame(200);

    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1@example.com', 'foo');
        $user2 = $this->createUser('user2@example.com', 'foo');
        $this->createUser('admin@example.com', 'foo', ['ROLE_ADMIN']);

        $cheese1 = $this->createCheeseListing('cheese1', 'cheese', 1000, $user1);
        $cheese2 = $this->createCheeseListing('cheese2', 'cheese', 1000, $user1, true);

        $this->login($client, 'user2@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/' . $cheese1->getId(), [
            'json' => [
                'title' => 'new title'
            ]
        ]);
        $this->assertResponseStatusCodeSame(
            404,
            'Query extension: Cheese is not published and only owner can edit this cheese'
        );

        $client->request('PUT', '/api/cheeses/' . $cheese2->getId(), [
            'json' => [
                'title' => 'new title'
            ]
        ]);
        $this->assertResponseStatusCodeSame(
            404,
            'Query extension: cheese is published but invalid owner'
        );

        $this->login($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/' . $cheese1->getId(), [
            'json' => [
                'title' => 'new title'
            ]
        ]);
        $this->assertResponseStatusCodeSame(
            404,
            'Query extension: Valid owner but only published cheese can be edit'
        );

        $client->request('PUT', '/api/cheeses/' . $cheese2->getId(), [
            'json' => [
                'title' => 'new title'
            ]
        ]);
        $this->assertResponseStatusCodeSame(
            200,
            'Query extension: Valid owner and cheese is published'
        );

        $this->login($client, 'admin@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/' . $cheese1->getId(), [
            'json' => [
                'title' => 'Admin title'
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    private function createCheeseListing(
        string $title,
        string $description,
        int $price,
        User $owner,
        bool $isPublished = false
    ): CheeseListing
    {
        $cheeseListing = new CheeseListing();
        $cheeseListing
            ->setTitle($title)
            ->setDescription($description)
            ->setPrice($price)
            ->setOwner($owner)
            ->setIsPublished($isPublished);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        return $cheeseListing;
    }
}