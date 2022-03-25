<?php

namespace App\Tests\functional;

use App\Test\CustomApiTestCase;

class DailyStatsResourceTest extends CustomApiTestCase
{
    public function testGetDailyStatsCollection()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'hydra:totalItems' => 1,
            'hydra:member' => [
                0 => [
                    'totalVisitors' => 100,
                    'mostPopularListings' => []
                ]
            ]
        ]);
    }

    public function testGetDailyStatsItem()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats/2022-02-25');
        $this->assertResponseStatusCodeSame(404);
    }
}