<?php

namespace App\Tests\functional;

use App\Repository\DailyStatsRepository;
use App\Test\CustomApiTestCase;

class DailyStatsResourceTest extends CustomApiTestCase
{
    public function testGetDailyStatsCollection()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'hydra:totalItems' => 30
        ]);

        $client->request('GET', '/api/daily-stats?from=2020-09-01');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'hydra:totalItems' => 3
        ]);
    }

    public function testGetDailyStatsItem()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats/2020-09-02');
        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains([
            'totalVisitors' => 2435
        ]);
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
        $this->assertResponseStatusCodeSame(
            422,
            "There are already one daily stats for today, come back tomorrow"
        );

        // Remove daily stats created by test
        // see constraint: App/Validator/IsUniqueStatsValidator
        $dailyStatsRepository->remove($dateString);
    }
}