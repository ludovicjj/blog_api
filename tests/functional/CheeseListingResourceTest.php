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
        $loadedObject = $this->loadFixtures(['tests/fixtures/cheese_listing/create_cheese_listing.yaml']);
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
        $this->loadFixtures(['tests/fixtures/cheese_listing/get_collection_cheese_listing.yaml']);

        // Check anonymous user have access to published cheese listings
        // (see: App/Doctrine/CheeseListingIsPublishedExtension)
        $client->request('GET', '/api/cheeses');
        $this->assertJsonContains(['hydra:totalItems' => 2]);
    }

    public function testGetCheeseListingItem()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/cheese_listing/get_item_cheese_listing.yaml']);
        $cheeseId = $loadedObject['cheese_1']->getId();

        $client->request('GET', '/api/cheeses/'.$cheeseId);
        $this->assertResponseStatusCodeSame(404, "Only admin can read not published cheese listing");

        // User::getPublishedCheeseListings return only published cheese listings
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
        $loadedObject = $this->loadFixtures(['tests/fixtures/cheese_listing/update_cheese_listing.yaml']);
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
        $loadedObject = $this->loadFixtures(['tests/fixtures/cheese_listing/publish_cheese_listing.yaml']);
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

    public function testPublishValidationCheeseListing()
    {
        $client = self::createClient();
        $loadedObject = $this->loadFixtures(['tests/fixtures/cheese_listing/publish_validation_cheese_listing.yaml']);

        // 1) the owner CANNOT publish with a short description
        // Custom constraint IsValidPublish check description length
        $this->login($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$loadedObject['cheese_1']->getId(), [
            'json' => ['isPublished' => true]
        ]);
        $this->assertResponseStatusCodeSame(422, 'Cannot publish : description is too short');

        // 2) a user CAN still make any changes as long as the cheese is unpublished
        $this->login($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$loadedObject['cheese_1']->getId(), [
            'json' => ['description' => 'short']
        ]);
        $cheese = $this->refreshEntity(CheeseListing::class, ['id' => $loadedObject['cheese_1']->getId()]);
        $this->assertResponseStatusCodeSame(200, 'Description is too short BUT this cheese is unpublished');
        $this->assertEquals('short', $cheese->getDescription());

        // 3) an admin user CAN publish with a short description
        // Custom constraint IsValidPublish let admin do even if description is too short
        $this->login($client, 'admin@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$loadedObject['cheese_1']->getId(), [
            'json' => ['isPublished' => true]
        ]);
        $this->assertResponseStatusCodeSame(200, 'Admin user can publish with short description');
        $cheese = $this->refreshEntity(CheeseListing::class, ['id' => $loadedObject['cheese_1']->getId()]);
        $this->assertTrue($cheese->getIsPublished());

        // 4) a user CANNOT unpublish cheese
        $this->login($client, 'user1@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$loadedObject['cheese_1']->getId(), [
            'json' => ['isPublished' => false]
        ]);
        $this->assertResponseStatusCodeSame(422, 'Only admin can unpublish');

        // 5) a user CAN unpublish cheese
        $this->login($client, 'admin@example.com', 'foo');
        $client->request('PUT', '/api/cheeses/'.$loadedObject['cheese_1']->getId(), [
            'json' => ['isPublished' => false]
        ]);
        $this->assertResponseStatusCodeSame(200, 'Admin user can unpublish');
        $cheese = $this->refreshEntity(CheeseListing::class, ['id' => $loadedObject['cheese_1']->getId()]);
        $this->assertFalse($cheese->getIsPublished());
    }
}