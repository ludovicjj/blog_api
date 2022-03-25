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
            'controller' => NotFoundAction::class,
            'read' => false,
            'output' => false
        ]
    ],
    shortName: "daily-stats",
)]
class DailyStats
{
    #[Groups(['daily-stats:read'])]
    private DateTimeInterface $date;

    #[Groups(['daily-stats:read'])]
    private int $totalVisitors;

    #[Groups(['daily-stats:read'])]
    private array $mostPopularListings;

    /**
     * @param DateTimeInterface $date
     * @param int $totalVisitors
     * @param array|CheeseListing[] $mostPopularListings
     */
    public function __construct(\DateTimeInterface $date, int $totalVisitors, array $mostPopularListings)
    {
        $this->date = $date;
        $this->totalVisitors = $totalVisitors;
        $this->mostPopularListings = $mostPopularListings;
    }

    /**
     * @param DateTimeInterface $date
     * @return $this
     */
    public function setDate(DateTimeInterface $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDate(): DateTimeInterface
    {
        return $this->date;
    }

    /**
     * @param int $totalVisitors
     * @return self
     */
    public function setTotalVisitors(int $totalVisitors): self
    {
        $this->totalVisitors = $totalVisitors;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalVisitors(): int
    {
        return $this->totalVisitors;
    }

    /**
     * @param array $listing
     * @return $this
     */
    public function setMostPopularListings(array $listing): self
    {
        $this->mostPopularListings = $listing;
        return $this;
    }

    /**
     * @return array
     */
    public function getMostPopularListings(): array
    {
        return $this->mostPopularListings;
    }

    #[ApiProperty(identifier: true)]
    public function getDateString(): string
    {
        return $this->getDate()->format('Y-m-d');
    }
}