<?php

namespace App\Service;

use App\Entity\DailyStats;
use App\Repository\PostRepository;
use DateTimeImmutable;

class StatsHelper
{
    public function __construct(private PostRepository $postRepository)
    {
    }

    public function fetchStatsData(): array
    {
        $statsData = json_decode(file_get_contents(__DIR__ . '/fake_stats.json'), true);
        return $statsData['stats'];
    }

    public function count(): int
    {
        return count($this->fetchStatsData());
    }

    private function createDailyStats($statsData): DailyStats
    {
        $mostPopularListings = $this->postRepository->findBy([], [], 4);

        return new DailyStats(
            new DateTimeImmutable($statsData['date']),
            $statsData['visitors'],
            $mostPopularListings
        );
    }

    public function fetchOne(string $date): ?DailyStats
    {
        foreach ($this->fetchStatsData() as $statData) {
            if ($statData['date'] === $date) {
                return $this->createDailyStats($statData);
            }
        }
        return null;
    }

    /**
     * @return array<DailyStats>
     */
    public function fetchMany(int $itemsPerPage = null, int $offset = null): array
    {
        $stats = [];
        $i = 0;
        foreach ($this->fetchStatsData() as $statData) {
            $i++;
            // Todo: offset;
            if ($offset >= $i) {
                continue;
            }

            $stats[] = $this->createDailyStats($statData);

            // Todo: items per page;
            if ($itemsPerPage <= count($stats)) {
                break;
            }
        }
        return $stats;
    }

    public function persist(DailyStats $dailyStat): void
    {
        $dailyStatsDateInput = $dailyStat->date->format('Y-m-d');
        $statsDataUpdated = [];

        foreach ($this->fetchStatsData() as $data) {
            if ($data['date'] === $dailyStatsDateInput) {
                $data['visitors'] = $dailyStat->totalVisitors;
            }
            $statsDataUpdated['stats'][] = $data;
        }

        file_put_contents(
            __DIR__ . '/fake_stats.json',
            json_encode($statsDataUpdated, JSON_PRETTY_PRINT)
        );
    }
}