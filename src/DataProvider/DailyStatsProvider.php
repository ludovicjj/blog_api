<?php

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use App\Entity\DailyStats;
use App\Repository\PostRepository;

class DailyStatsProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    private $postRepository;

    public function __construct(PostRepository $postRepository)
    {
        $this->postRepository = $postRepository;
    }

    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $listing = $this->postRepository->findBy([], [], 4);

        $stats1 = new DailyStats(
            new \DateTime(),
            100,
            $listing
        );
        $stats2 = new DailyStats(
            new \DateTime('-1 day'),
            200,
            $listing
        );

        return [$stats1, $stats2];
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return $resourceClass === DailyStats::class;
    }
}