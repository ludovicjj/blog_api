<?php

namespace App\DataPersister;

use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use App\Entity\DailyStats;
use App\Repository\DailyStatsRepository;

class DailyStatsPersister implements DataPersisterInterface
{

    public function __construct(private DailyStatsRepository $dailyStatsRepository)
    {
    }

    public function supports($data): bool
    {
        return $data instanceof DailyStats;
    }

    /**
     * @param DailyStats $data
     * @return void
     */
    public function persist($data): void
    {
        // TODO : refactorings pour la création.
        // TODO : prend seulement en charge la modification (PUT)
        $this->dailyStatsRepository->update($data);
    }

    public function remove($data)
    {
        throw new \Exception('not implemented yet');
    }
}