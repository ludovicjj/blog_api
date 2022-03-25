<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;

class DailyStatsProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $dailyStats = (new DailyStats())
            ->setDate(new \DateTime())
            ->setTotalVisitors(100)
            ->setMostPopularListings([]);

        return [$dailyStats];
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }
}