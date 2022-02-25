<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;

#[ApiResource(
    collectionOperations: [
        'get'
    ],
    itemOperations: [],
    shortName: "daily-stats"
)]
class DailyStats
{
    #[ApiProperty(identifier: true)]
    public $date;

    public $totalVisitors;

    public $mostPopularListings;
}