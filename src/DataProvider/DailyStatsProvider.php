<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\Pagination;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;
use App\Filter\DailyStatsFilter;
use App\Service\StatsHelper;

class DailyStatsProvider implements ContextAwareCollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private StatsHelper $statsHelper, private Pagination $pagination)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = []): iterable
    {
        // get page, offset and limit
        // to get page, provider must implement ContextAwareCollectionDataProviderInterface, else it's always 1
        // no need offset, I calculated by myself
        // limit is define with annotation into DailyStats entity (option: paginationItemsPerPage)
        list($page,, $limit) = $this->pagination->getPagination($resourceClass, $operationName, $context);

        $paginator = new DailyStatsPaginator($this->statsHelper, $page , $limit);

        // Use fromDate passed into context by DailyStatsFilter
        $fromDate = $context[DailyStatsFilter::FROM_FILTER_CONTEXT] ?? null;

        if ($fromDate) {
            $paginator->setFromDate($fromDate);
        }

        return $paginator;
    }

    public function getItem(string $resourceClass, $id, string $operationName = null, array $context = []): ?DailyStats
    {
        return $this->statsHelper->getOne($id);
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }
}