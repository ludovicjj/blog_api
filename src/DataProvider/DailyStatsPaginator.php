<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use Exception;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private $dailyStatsIterator;

    public function count(): int
    {
        return $this->getTotalItems();
    }

    public function getTotalItems(): float
    {
        return 25;
    }

    public function getCurrentPage(): float
    {
        return 1;
    }

    public function getLastPage(): float
    {
        return 2;
    }

    public function getItemsPerPage(): float
    {
        return 10;
    }

    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $this->dailyStatsIterator = new \ArrayIterator([]);
        }

        return $this->dailyStatsIterator;
    }
}