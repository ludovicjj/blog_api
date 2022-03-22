<?php

namespace App\Tests\functional;

use App\Entity\CheeseListing;
use App\Entity\CheeseNotification;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\RecreateDatabaseTrait;

class CheeseListingResourceTest extends CustomApiTestCase
{
    use RecreateDatabaseTrait;

    public function testCreateCheeseListing()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/create_cheese_listing.yaml']);
        $client->request("POST", "/api/cheeses", [
            'json' => []
        ]);
        $this->assertResponseStatusCodeSame(401, 'You must be authenticated');

        $this->login($client, 'user1@example.com', 'foo');
        $client->request('POST', '/api/cheeses', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(422, 'Validation violation');

        $cheeseData = [
            'title' => 'Mystery cheese... kinda green',
            'description' => 'What mysteries does it hold?',
            'price' => 5000
        ];

        // If any owner is defined into body then cheese owner is authenticated user
        $client->request('POST', '/api/cheeses', ['json' => $cheeseData]);
        $this->assertResponseStatusCodeSame(201);

        $client->request("POST", "/api/cheeses", [
            'json' => $cheeseData + ['owner' => '/api/users/'. $loadedObject['user_2']->getId()]
        ]);
        $this->assertResponseStatusCodeSame(422, 'Custom constraint IsValidOwner: not passing the correct owner');

        $client->request("POST", "/api/cheeses", [
            'json' => $cheeseData + ['owner' => '/api/users/'. $loadedObject['user_1']->getId()]
        ]);
        $this->assertResponseStatusCodeSame(201);
    }

    public function testGetCheeseListingCollection()
    {
        $client = self::createClient();
        $this->loadFixtures(['tests/fixtures/get_collection_cheese_listing.yaml']);

        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/get_item_cheese_listing.yaml']);
        $cheeseId = $loadedObject['cheese_1']->getId();

        $client->request('GET', '/api/cheeses/'.$cheeseId);
        $this->assertResponseStatusCodeSame(404, "Only admin can read not published this cheese listing");

        // Check filter return only published cheese listing
        $this->login($client,'user1@example.com', 'foo');
        $response = $client->request('GET', '/api/users/'.$loadedObject['user_1']->getId());
        $data = $response->toArray();
        $this->assertEmpty($data['cheeseListings']);

        $this->login($client, 'admin@example.com', 'foo');
        $client->request('GET', '/api/cheeses/'.$cheeseId);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testUpdateCheeseListing()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/update_cheese_listing.yaml']);
        $cheeseId = $loadedObject['cheese_1']->getId();

        $this->login($client, 'user2@example.com', 'foo');

        $client->request('PUT', '/api/cheeses/'.$cheeseId, [
            'json' => ['title' => 'updated']
        ]);
        $this->assertResponseStatusCodeSame(403, 'Voter allow only owner or admin to edit this cheese');

        $this->login($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$cheeseId, [
            'json' => ['title' => 'updated']
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    public function testPublishCheeseListing()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/publish_cheese_listing.yaml']);
        $cheeseId = $loadedObject['cheese_1']->getId();
        $em = $this->getEntityManager();

        $this->login($client, 'user@example.com', 'foo');

        $client->request('PUT', '/api/cheeses/'.$cheeseId, [
            'json' => ['isPublished' => true]
        ]);
        $this->assertResponseStatusCodeSame(200);

        /** @var CheeseListing $cheeseListing */
        $cheeseListing = $em->getRepository(CheeseListing::class)->findOneBy(['id' => $cheeseId]);
        $this->assertTrue($cheeseListing->getIsPublished());

        $cheeseNotificationCount = $em->getRepository(CheeseNotification::class)->count([]);
        $this->assertEquals(1, $cheeseNotificationCount);

        // Update again the same cheese and get only one notification
        $client->request('PUT', '/api/cheeses/'.$cheeseId, [
            'json' => ['isPublished' => true]
        ]);
        $cheeseNotificationCount = $em->getRepository(CheeseNotification::class)->count([]);
        $this->assertEquals(1, $cheeseNotificationCount);
    }
}