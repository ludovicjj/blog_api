<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\ItemDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;
use App\Service\StatsHelper;

class DailyStatsProvider implements CollectionDataProviderInterface, ItemDataProviderInterface, RestrictedDataProviderInterface
{
    public function __construct(private StatsHelper $statsHelper)
    {
    }

    public function getCollection(string $resourceClass, string $operationName = null): iterable
    {
        return new DailyStatsPaginator($this->statsHelper, 1 , 3);
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