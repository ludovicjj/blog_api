<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CheeseListingRepository;
use DateTimeInterface;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ORM\Table(name="cheese_listing")
 */
#[ApiResource(
    collectionOperations: ['get', 'post'],
    itemOperations: ['get', 'put'],
    shortName: 'cheeses',
    denormalizationContext: ['groups' => ['cheeses:write'], 'swagger_definition_name' => 'Write'],
    normalizationContext: ['groups' => ['cheeses:read'], 'swagger_definition_name' => 'Read'],
)]
class CheeseListing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['cheeses:read', 'cheeses:write'])]
    private string $title;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['cheeses:read'])]
    private string $description;

    /**
     * @ORM\Column(type="integer")
     */
    #[Groups(['cheeses:read', 'cheeses:write'])]
    private int $price;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isPublished;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isPublished = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * The description of the cheese as raw text.
     * @param string $description
     * @return $this
     */
    #[Groups(['cheeses:write'])]
    #[SerializedName('description')]
    public function setTextDescription(string $description): self
    {
        $this->description = str_replace(["\r\n", "\r", "\n"], "<br />", $description);
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * How long ago this cheese item was added in text format, example "1 day ago".
     * @return string
     */
    #[Groups(['cheeses:read'])]
    public function getCreatedAtAgo(): string
    {
        return Carbon::instance($this->getCreatedAt())->diffForHumans();
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function getIsPublished(): ?bool
    {
        return $this->isPublished;
    }
}