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
    private DateTimeInterface $date;

    private int $totalVisitors;

    private array $mostPopularListings;

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
}