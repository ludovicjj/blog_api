<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private ?Traversable $dailyStatsIterator = null;

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
        return iterator_count($this->getIterator());
    }

    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $this->dailyStatsIterator = new \ArrayIterator($this->statsHelper->fetchMany());
        }

        return $this->dailyStatsIterator;
    }
}