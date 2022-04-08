<?php

namespace App\Dto;

use App\Entity\CheeseListing;
use App\Entity\User;
use Carbon\Carbon;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    /**
     * The title of this listing
     *
     * @var string $title
     */
    #[Groups(['cheese:read', 'user:read'])]
    public string $title;

    /**
     * @var string
     */
    #[Groups(['cheese:read'])]
    public string $description;

    /**
     * @var integer
     */
    #[Groups(['cheese:read', 'user:read'])]
    public int $price;

    public \DateTimeInterface $createdAt;

    #[Groups(['cheese:read'])]
    public User $owner;

    /**
     * Get a part of description limited to 40 characters.
     */
    #[Groups(['cheese:read'])]
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }
        return substr($this->description, 0, 40).'...';
    }

    /**
     * How long ago this cheese item was added in text format, example "1 day ago".
     * @return string
     */
    #[Groups(['cheese:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->createdAt)->diffForHumans();
    }

    public static function createFromEntity(CheeseListing $cheeseListing): self
    {
        $output = new CheeseListingOutput();
        $output->title = $cheeseListing->getTitle();
        $output->description = $cheeseListing->getDescription();
        $output->price = $cheeseListing->getPrice();
        $output->createdAt = $cheeseListing->getCreatedAt();
        $output->owner = $cheeseListing->getOwner();

        return $output;
    }
}