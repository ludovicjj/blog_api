<?php

namespace App\Repository;

use App\Entity\DailyStats;

class DailyStatsRepository
{
    public function __construct(private string $rootDir)
    {
    }

    /**
     * Add or update data to json file
     *
     * @param DailyStats $dailyStats
     * @return void
     */
    public function persist(DailyStats $dailyStats): void
    {
        $statsData = $this->getStatsData();
        $isUpdated = false;
        $stats = &$statsData['stats'];

        foreach ($stats as $key => $stat) {
            // update existing data
            if ($dailyStats->getDateString() === $stat['date']) {
                $isUpdated = true;
                $stats[$key]['visitors'] = $dailyStats->getTotalVisitors();
            }
        }

        // adding new data
        if (!$isUpdated) {
            $stats[] = [
                'date' => $dailyStats->getDateString(),
                'visitors' => $dailyStats->getTotalVisitors()
            ];
        }

        file_put_contents(
            $this->rootDir . '/src/Service/fake_stats.json',
            json_encode($statsData, JSON_PRETTY_PRINT)
        );
    }

    /**
     * Build one Daily Stats for the given date
     *
     * @param string $date expected date format: Y-m-d
     * @return DailyStats|null
     */
    public function find(string $date): ?DailyStats
    {
        $statsData = $this->getStatsData();
        foreach ($statsData['stats'] as $stat) {
            if ($stat['date'] === $date) {
                return (new DailyStats())
                    ->setDate(new \DateTimeImmutable($stat['date']))
                    ->setTotalVisitors($stat['visitors'])
                    ->setMostPopularListings([]);
            }
        }
        return null;
    }

    /**
     * Remove data to json file for the given data
     *
     * @param string $date expected date format: Y-m-d
     * @return void
     */
    public function remove(string $date): void
    {
        $statsData = $this->getStatsData();
        $stats = &$statsData['stats'];

        foreach ($stats as $key => $stat) {
            if ($stat['date'] === $date) {
                unset($stats[$key]);
            }
        }

        file_put_contents(
            $this->rootDir . '/src/Service/fake_stats.json',
            json_encode($statsData, JSON_PRETTY_PRINT)
        );
    }

    /**
     * @return array{_comment: array, stats: array}
     */
    private function getStatsData(): array
    {
        $path = $this->rootDir . '/src/Service/fake_stats.json';
        return json_decode(file_get_contents($path), true);
    }
}