<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;
use Psr\Log\LoggerInterface;

class DailyStatsPersister implements DataPersisterInterface
{
    public function __construct(private LoggerInterface $appLogger)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof DailyStats;
    }

    /**
     * @param DailyStats $data
     */
    public function persist($data): void
    {
        $this->appLogger->info(sprintf('Update the  total visitor to "%d"', $data->totalVisitors));
    }

    public function remove($data)
    {
        throw new \Exception('Not supported');
    }
}