<?php

namespace App\Repository;

use App\Entity\DailyStats;

class DailyStatsRepository
{
    public function __construct(private string $rootDir)
    {
    }

    public function persist(DailyStats $dailyStats)
    {
        // TODO implement update json with input data
    }

    public function update(DailyStats $dailyStats): void
    {
        $statsData = $this->findAll();
        $stats = &$statsData['stats'];

        foreach ($stats as $key => $stat) {
            if ($dailyStats->getDateString() === $stat['date']) {
                $stats[$key]['visitors'] = $dailyStats->getTotalVisitors();
            }
        }

        file_put_contents(
            $this->rootDir . '/src/Service/fake_stats.json',
            json_encode($statsData, JSON_PRETTY_PRINT)
        );
    }

    public function find(string $date): ?DailyStats
    {
        $statsData = $this->findAll();
        foreach ($statsData['stats'] as $stat) {
            if ($stat['date'] === $date) {
                return new DailyStats(
                    new \DateTimeImmutable($stat['date']),
                    $stat['visitors'],
                    []
                );
            }
        }
        return null;
    }

    public function findAll(): array
    {
        $path = $this->rootDir . '/src/Service/fake_stats.json';
        return json_decode(file_get_contents($path), true);
    }
}