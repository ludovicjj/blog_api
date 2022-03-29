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

    /**
     * @param DailyStats $data
     * @return void
     */
    public function persist($data): void
    {
        $statsData = json_decode(file_get_contents(__DIR__ . '/../Service/fake_stats.json'), true);
        $stats = &$statsData['stats'];

        foreach ($stats as $key => $stat) {
            if ($data->getDateString() === $stat['date']) {
                $stats[$key]['visitors'] = $data->getTotalVisitors();
            }
        }

        file_put_contents(
            __DIR__ . '/../Service/fake_stats.json',
            json_encode($statsData, JSON_PRETTY_PRINT)
        );

    }

    public function remove($data)
    {
        throw new \Exception('not implemented yet');
    }
}