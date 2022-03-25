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
    }

    public function testGetDailyStatsItem()
    {
        $client = static::createClient();
        $client->request('GET', '/api/daily-stats/2022-02-25');
        $this->assertResponseStatusCodeSame(404);
    }
}