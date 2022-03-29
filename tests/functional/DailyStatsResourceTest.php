<?php

namespace App\Tests\functional;

use App\Repository\DailyStatsRepository;
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
        /** @var DailyStatsRepository $dailyStatsRepository */
        $dailyStatsRepository = $container->get('App\Repository\DailyStatsRepository');

        $client->request('PUT', '/api/daily-stats/2020-09-03', [
            'json' => [
                'totalVisitors' => 18
            ]
        ]);
        $this->assertResponseStatusCodeSame(200);
        $dailyStats = $dailyStatsRepository->find('2020-09-03');
        $this->assertEquals(18, $dailyStats->getTotalVisitors());
    }
}