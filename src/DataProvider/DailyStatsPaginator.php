<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;
use App\Service\StatsHelper;
use Traversable;
use DateTimeInterface;
use ArrayIterator;

class DailyStatsPaginator implements PaginatorInterface, \IteratorAggregate
{
    /**
     * @var ArrayIterator|null
     */
    private ?ArrayIterator $dailyStatsIterator = null;

    /**
     * @var DateTimeInterface|null
     */
    private ?DateTimeInterface $fromDate = null;

    public function __construct(
        private StatsHelper $statsHelper,
        private int $currentPage,
        private int $itemsPerPage,
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
        $criteria = [];
        if ($this->fromDate) {
            $criteria['from'] = $this->fromDate;
        }
        return $this->statsHelper->count($criteria);
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
            $offset = ($this->getCurrentPage() - 1) * $this->getItemsPerPage();

            $criteria = [];

            if ($this->fromDate) {
                $criteria['from'] = $this->fromDate;
            }

            $this->dailyStatsIterator = new ArrayIterator(
                $this->statsHelper->getMany(
                    $this->getItemsPerPage(),
                    $offset,
                    $criteria
                )
            );
        }

        return $this->dailyStatsIterator;
    }

    public function setFromDate(DateTimeInterface $fromDate)
    {
        $this->fromDate = $fromDate;
    }
}