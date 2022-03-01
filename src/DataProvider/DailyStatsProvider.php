<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\ContextAwareCollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;
use App\Service\StatsHelper;

class DailyStatsProvider implements ContextAwareCollectionDataProviderInterface, RestrictedDataProviderInterface,
    ItemDataProviderInterface
{

    public function __construct(private StatsHelper $statsHelper)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null, array $context = [])
    {
        $currentPageQuery = (int)$context['filters']['page'];
        return new DailyStatsPaginator($this->statsHelper, $currentPageQuery, 3);
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