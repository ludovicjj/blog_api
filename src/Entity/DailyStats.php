<?php

namespace App\Entity;

use ApiPlatform\Core\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;

#[ApiResource(
    collectionOperations: [
        'get'
    ],
    itemOperations: [
        'get' => [
            'read' => false,
            'output' => false,
            'controller' => NotFoundAction::class
        ]
    ],
    shortName: "daily-stats",
    normalizationContext: ['groups' => ['read:daily-stats:collection']]
)]
class DailyStats
{
    #[Groups(['read:daily-stats:collection'])]
    public DateTimeInterface $date;

    #[Groups(['read:daily-stats:collection'])]
    public int $totalVisitors;

    /** @var array<Post> */
    #[Groups(['read:daily-stats:collection'])]
    public array $mostPopularListings;

    public function __construct(DateTimeInterface $date, int $totalVisitors, array $mostPopularListings)
    {
        $this->date = $date;
        $this->totalVisitors = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }

    #[ApiProperty(identifier: true)]
    public function getDateString(): string
    {
        return $this->date->format('Y-m-d');
    }
}