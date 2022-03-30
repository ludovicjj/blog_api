<?php

namespace App\Service;

use App\Entity\DailyStats;
use App\Repository\CheeseListingRepository;
use DateTimeImmutable;

class StatsHelper
{
    public function __construct(private CheeseListingRepository $cheeseListingRepository)
    {
    }

    /**
     * Load fake data
     * @return array
     */
    private function fetchStatsData(): array
    {
        $statsData = json_decode(file_get_contents(__DIR__ . '/fake_stats.json'), true);
        return $statsData['stats'];
    }

    /**
     * Count fake data
     * @return int
     */
    public function count(): int
    {
        return count($this->fetchStatsData());
    }

    /**
     * Fetch many DailyStats wit criteria (soon)
     */
    public function getMany(int $limit = null, int $offset = null): array
    {
        $dailyStatsOutput = [];
        $i = 0;

        foreach ($this->fetchStatsData() as $statsData) {
            $i++;

            // offset
            if ($offset >= $i) {
                continue;
            }

            $dailyStatsOutput[] = $this->buildDailyStatsObject($statsData);

            // limit
            if (count($dailyStatsOutput) >= $limit) {
                break;
            }
        }

        return $dailyStatsOutput;
    }

    /**
     * Fetch one DailyStats with identifier: date
     *
     * @param string $date format : Y-m-d
     * @return null|DailyStats
     */
    public function getOne(string $date): ?DailyStats
    {
        foreach ($this->fetchStatsData() as $statsData) {
            if ($statsData['date'] === $date) {
                return $this->buildDailyStatsObject($statsData);
            }
        }

        return null;
    }

    /**
     * Build DailyStats object using fake data
     */
    private function buildDailyStatsObject(array $statsData): DailyStats
    {
        $cheeseListing = $this->cheeseListingRepository->findBy([], [], 5);

        return (new DailyStats())
            ->setDate(new DateTimeImmutable($statsData['date']))
            ->setTotalVisitors($statsData['visitors'])
            ->setMostPopularListings($cheeseListing);
    }
}