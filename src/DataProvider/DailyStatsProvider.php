<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;
use App\Filter\DailyStatsFilter;
use App\Service\StatsHelper;

class DailyStatsProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface,
    ItemDataProviderInterface
{

    public function __construct(private StatsHelper $statsHelper, private Pagination $pagination)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        list($page, $offset, $itemsPerPage) = $this->pagination->getPagination($resourceClass, $operationName, $context);

        $paginator =  new DailyStatsPaginator($this->statsHelper, $page, $itemsPerPage);
        $fromDate = $context[DailyStatsFilter::FROM_FILTER_CONTEXT] ?? null;
        if ($fromDate) {
            $paginator->setFromDate($fromDate);
        }

        return $paginator;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?object
    {
        return $this->statsHelper->fetchOne($id);
    }
}