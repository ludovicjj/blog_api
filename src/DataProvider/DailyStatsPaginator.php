<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Entity\DailyStats;
use App\Service\StatsHelper;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private ?Traversable $dailyStatsIterator;

    public function __construct(private StatsHelper $statsHelper)
    {
    }

    public function getLastPage(): float
    {
        return 2;
    }

    public function getTotalItems(): float
    {
        return 25;
    }

    public function getCurrentPage(): float
    {
        return 1;
    }

    public function getItemsPerPage(): float
    {
        return 10;
    }

    public function count(): int
    {
        return $this->getTotalItems();
    }

    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $stats = [];
            $stats1 = new DailyStats(
                new \DateTime(),
                100,
                []
            );
            $stats2 = new DailyStats(
                new \DateTime('-1 day'),
                200,
                []
            );
            $stats = [$stats1, $stats2];

            $this->dailyStatsIterator = new \ArrayIterator($stats);
        }

        return $this->dailyStatsIterator;
    }
}