<?php

namespace App\Dto;

use App\Entity\User;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class CheeseListingInput
{
    #[Groups(['cheese:write', 'user:write'])]
    public ?string $title = null;

    #[Groups(['cheese:write', 'user:write'])]
    public ?int $price = null;

    #[Groups(['cheese:collection:post'])]
    public ?User $owner = null;

    #[Groups(['cheese:write'])]
    public bool $isPublished = false;

    public ?string $description = null;

    /**
     * The description of the cheese as raw text.
     * @param string $description
     */
    #[Groups(['cheese:write', 'user:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description)
    {
        $this->description = str_replace(["\r\n", "\r", "\n"], "<br />", $description);
    }
}