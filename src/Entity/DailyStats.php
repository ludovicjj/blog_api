<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;

#[ApiResource(
    collectionOperations: [
        'get'
    ],
    itemOperations: [],
    shortName: "daily-stats",
)]
class DailyStats
{
    public DateTimeInterface $date;

    public int $totalVisitors;

    public array $mostPopularListings;
}