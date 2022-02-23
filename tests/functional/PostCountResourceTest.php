<?php

namespace App\Tests\functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;

class PostCountResourceTest extends ApiTestCase
{
    public function testPostCount()
    {
        $client = self::createClient();
        $client->request('GET', '/api/posts/count');
        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');
        $this->assertJsonContains([
            'posts' => 0
        ]);

    }
}