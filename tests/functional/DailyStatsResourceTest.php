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

    public function testCreateDailyStats()
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var DailyStatsRepository $dailyStatsRepository */
        $dailyStatsRepository = $container->get('App\Repository\DailyStatsRepository');

        $dateString = (new \DateTimeImmutable('now'))->format('Y-m-d');
        // clear daily stats if already exist one for today
        // see constraint: APP/Validator/IsUniqueStatsValidator
        $dailyStatsRepository->remove($dateString);

        $client->request('POST', '/api/daily-stats', [
            'json' => [
                'totalVisitors' => 42
            ]
        ]);
        $this->assertResponseStatusCodeSame(201);

        $dailyStats = $dailyStatsRepository->find($dateString);
        $this->assertEquals(42, $dailyStats->getTotalVisitors());

        // prevent to create daily stats is one already exist for this day
        $client->request('POST', '/api/daily-stats', [
            'json' => [
                'totalVisitors' => 123
            ]
        ]);
        $this->assertResponseStatusCodeSame(422, "There are already one daily stats for today, come back tomorrow");
    }
}