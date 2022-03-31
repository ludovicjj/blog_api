<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiProperty;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use DateTimeInterface;
use App\Validator\IsUniqueStats;

#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get',
        'put'
    ],
    shortName: "daily-stats",
    paginationItemsPerPage: 7
)]
/**
 * @IsUniqueStats()
 */
class DailyStats
{
    #[Groups(['daily-stats:read'])]
    private ?DateTimeInterface $date = null;

    #[Groups(['daily-stats:read', 'daily-stats:write'])]
    private ?int $totalVisitors = null;

    /**
     * The 5 most popular cheese listings from this date!
     * @var array<CheeseListing>|array
     */
    #[Groups(['daily-stats:read'])]
    private array $mostPopularListings = [];


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
     * @return DateTimeInterface|null
     */
    public function getDate(): ?DateTimeInterface
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
     * @return int|null
     */
    public function getTotalVisitors(): ?int
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
        if (!$this->getDate()) {
            throw new \LogicException('The date field has not been initialized');
        }
        return $this->getDate()->format('Y-m-d');
    }
}