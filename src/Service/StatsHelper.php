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

    private function count(): int
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
    public function fetchMany(): array
    {
        $stats = [];
        foreach ($this->fetchStatsData() as $statData) {

            $stats[] = $this->createDailyStats($statData);
        }
        return $stats;
    }
}