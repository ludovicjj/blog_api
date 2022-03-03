<?php

namespace App\Service;

use App\Entity\DailyStats;
use App\Repository\PostRepository;
use DateTimeImmutable;
use DateTimeInterface;

class StatsHelper
{
    public function __construct(
        private PostRepository $postRepository,
        private ?array $criteria = null
    )
    {
    }

    public function fetchStatsData(): array
    {
        $statsData = json_decode(file_get_contents(__DIR__ . '/fake_stats.json'), true);
        return $statsData['stats'];
    }

    public function count(): int
    {
        $countStatsData = [];
        $fromDate = $this->getCriteria('from');

        if (!$fromDate) {
            return count($this->fetchStatsData());
        }

        foreach ($this->fetchStatsData() as $statData) {
            $dateString = $statData['date'];
            $date = new DateTimeImmutable($dateString);

            if ($fromDate && $fromDate > $date) {
                continue;
            }
            $countStatsData[] = $statData;
        }

        return count($countStatsData);
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
     * @param int|null $itemsPerPage
     * @param int|null $offset
     * @param array{from?: DateTimeInterface, to?: DateTimeInterface} $criteria Supported keys are:
     *  from DateTimeInterface
     *  to   DateTimeInterface
     *
     * @return array<DailyStats>
     * @throws \Exception
     */
    public function fetchMany(int $itemsPerPage = null, int $offset = null, array $criteria = []): array
    {
        $this->setCriteria($criteria);
        $fromDate = $this->getCriteria('from');
        $stats = [];
        $i = 0;
        foreach ($this->fetchStatsData() as $statData) {
            $i++;
            // Todo: offset;
            if ($offset >= $i) {
                continue;
            }

            // Todo filter: from
            $dateString = $statData['date'];
            $date = new DateTimeImmutable($dateString);
            if ($fromDate && $date < $fromDate) {
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

    private function setCriteria(array $criteria)
    {
        $this->criteria = $criteria;
    }

    private function getCriteria(string $key): ?DateTimeInterface
    {
        if (empty($this->criteria)) {
            return null;
        }

        return $this->criteria[$key] ?? null;
    }
}