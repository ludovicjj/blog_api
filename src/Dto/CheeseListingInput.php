<?php

namespace App\Dto;

use App\Entity\CheeseListing;
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

    /**
     * If is a creating operation (POST): there is no entity from context, so create a new one
     * If is an editing operation (PUT): there is entity from context into "object_to_populate"
     * Then update entity properties using DTO data
     * (used into CheeseListingInputDataTransformer)
     */
    public function createOrUpdateEntity(?CheeseListing $cheeseListing): CheeseListing
    {
        if (!$cheeseListing) {
            $cheeseListing = new CheeseListing();
        }
        $cheeseListing->setTitle($this->title);
        $cheeseListing->setDescription($this->description);
        $cheeseListing->setPrice($this->price);
        $cheeseListing->setOwner($this->owner);
        $cheeseListing->setIsPublished($this->isPublished);

        return $cheeseListing;
    }

    /**
     * Create a new CheeseListingInput
     * If is a creating operation (POST): there is no entity, so just return an empty DTO
     * If is an editing operation (PUT): there is entity, return DTO updated with entity data
     * (used into CheeseListingInputDenormalizer)
     */
    public static function createFromEntity(?CheeseListing $cheeseListing): self
    {
        $dto = new CheeseListingInput();

        // not an edit, so just return an empty DTO
        if (!$cheeseListing) {
            return $dto;
        }

        $dto->title = $cheeseListing->getTitle();
        $dto->description = $cheeseListing->getDescription();
        $dto->price = $cheeseListing->getPrice();
        $dto->isPublished = $cheeseListing->getIsPublished();
        $dto->owner = $cheeseListing->getOwner();

        return $dto;
    }
}