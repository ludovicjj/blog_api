<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;

class DailyStatsPersister implements DataPersisterInterface
{

    public function supports($data): bool
    {
        return $data instanceof DailyStats;
    }

    public function persist($data)
    {
        // TODO: Implement persist() method.
    }

    public function remove($data)
    {
        // TODO: Implement remove() method.
    }
}