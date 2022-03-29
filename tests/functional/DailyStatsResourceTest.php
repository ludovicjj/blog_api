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

    public function testUpdateDailyStats()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $statsPath = $container->getParameter('stats.data_path');

        $client->request('PUT', '/api/daily-stats/2020-09-03', [
            'json' => [
                'totalVisitors' => 152
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $data = json_decode(file_get_contents($statsPath), true);
        $this->assertEquals(152, $data['stats'][0]['visitors']);
    }
}