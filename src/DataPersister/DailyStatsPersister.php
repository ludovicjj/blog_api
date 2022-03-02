<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;
use App\Service\StatsHelper;

class DailyStatsPersister implements DataPersisterInterface
{
    public function __construct(private StatsHelper $statsHelper)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof DailyStats;
    }

    /**
     * @param DailyStats $data
     * @return DailyStats
     */
    public function persist($data): DailyStats
    {
        $this->statsHelper->persist($data);
        return $data;
    }

    public function remove($data)
    {
        throw new \Exception('Not supported yet');
    }
}