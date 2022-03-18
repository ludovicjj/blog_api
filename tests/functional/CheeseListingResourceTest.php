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

        $authenticatedUser = $this->createUserAndLogin($client, 'cheesetest@example.com', 'foo');
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

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1@example.com', 'foo');
        $user2 = $this->createUser('user2@example.com', 'foo');
        $this->createUser('admin@example.com', 'foo', ['ROLE_ADMIN']);

        $cheeseListing = new CheeseListing();
        $cheeseListing
            ->setTitle('Random title')
            ->setPrice(1000)
            ->setTextDescription('Random description')
            ->setOwner($user1);

        $em = $this->getEntityManager();
        $em->persist($cheeseListing);
        $em->flush();

        $this->login($client, 'admin@example.com', 'foo');

        $client->request('PUT', '/api/cheeses/' . $cheeseListing->getId(), [
            'json' => [
                'title' => 'updated',
                'owner' => '/api/users/'.$user2->getId()
            ]
        ]);
        $this->assertResponseStatusCodeSame(200, 'only author can update');
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