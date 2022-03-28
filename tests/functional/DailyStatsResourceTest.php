<?php

namespace App\Tests\functional;

use App\Test\CustomApiTestCase;

class DailyStatsResourceTest extends CustomApiTestCase
{
    public function testGetDailyStatsCollection()
    {
        $client = static::createClient();
        $response = $client->request('GET', '/api/daily-stats');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'hydra:totalItems' => 30,
        ]);
        $data = $response->toArray();
        $this->assertEquals(7, count($data['hydra:member']));
    }

    public function testGetDailyStatsItem()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats/2020-09-03');
        $this->assertResponseStatusCodeSame(200);
    }
}