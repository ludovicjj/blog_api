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
     *
     * @param array $criteria
     *      Supported keys are:
     *          * from DateTimeInterface
     *
     * @return int
     */
    public function count(array $criteria = []): int
    {
        $fromDate = $criteria['from'] ?? null;

        if (!$fromDate) {
            return count($this->fetchStatsData());
        }

        $countData = [];
        foreach ($this->fetchStatsData() as $statsData) {
            $dateString = $statsData['date'];
            $date = new DateTimeImmutable($dateString);

            if ($fromDate && $date < $fromDate) {
                continue;
            }
            $countData[] = $statsData;
        }
        return count($countData);
    }

    /**
     * Fetch many DailyStats wit criteria
     * @param array $criteria An array of criteria to limit the results:
     *      Supported keys are:
     *          * from DateTimeInterface
     */
    public function getMany(int $limit = null, int $offset = null, array $criteria = []): array
    {
        $fromDate = $criteria['from'] ?? null;
        $dailyStatsOutput = [];
        $i = 0;

        foreach ($this->fetchStatsData() as $statsData) {
            $i++;

            // offset
            if ($offset >= $i) {
                continue;
            }

            // from
            $dateString = $statsData['date'];
            $date = new \DateTimeImmutable($dateString);

            if ($fromDate && $date < $fromDate) {
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