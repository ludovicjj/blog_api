<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\PaginatorInterface;

class DailyStatsPaginator implements PaginatorInterface
{

    public function count()
    {
        return $this->getTotalItems();
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
}