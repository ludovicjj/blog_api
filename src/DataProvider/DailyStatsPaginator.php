<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use Traversable;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    private $dailyStatsIterator;

    public function __construct(
        private StatsHelper $statsHelper,
        private int $currentPage,
        private int $itemsPerPage
    )
    {
    }

    /**
     * the number of items on this page
     *
     * @return int
     * @throws \Exception
     */
    public function count(): int
    {
        return iterator_count($this->getIterator());
    }

    /**
     * the total number of results, not just the results on this page.
     * @return float
     */
    public function getTotalItems(): float
    {
        return $this->statsHelper->count();
    }

    public function getCurrentPage(): float
    {
        return $this->currentPage;
    }

    public function getLastPage(): float
    {
        return ceil($this->getTotalItems() / $this->getItemsPerPage()) ?: 1.;
    }

    public function getItemsPerPage(): float
    {
        return $this->itemsPerPage;
    }

    public function getIterator(): Traversable
    {
        if ($this->dailyStatsIterator === null) {
            $this->dailyStatsIterator = new \ArrayIterator(
                $this->statsHelper->getMany()
            );
        }

        return $this->dailyStatsIterator;
    }
}