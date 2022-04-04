<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;

class CheeseListingOutput
{
    /**
     * The title of this listing
     *
     * @var string $title
     */
    #[Groups(['cheese:read'])]
    public string $title;

    /**
     * @var string
     */
    #[Groups(['cheese:read'])]
    public string $description;

    /**
     * @var integer
     */
    #[Groups(['cheese:read'])]
    public int $price;

    /**
     * How long ago this cheese item was added in text format, example "1 day ago".
     *
     * @var string
     */
    #[Groups(['cheese:read'])]
    public string $createdAtAgo;

    #[Groups(['cheese:read'])]
    public User $owner;

    /**
     * Get a part of description limited to 40 characters.
     *
     * @Groups("cheese:read")
     */
    public function getShortDescription(): ?string
    {
        if (strlen($this->description) < 40) {
            return $this->description;
        }
        return substr($this->description, 0, 40).'...';
    }
}