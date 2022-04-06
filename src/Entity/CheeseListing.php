<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Dto\CheeseListingInput;
use App\Dto\CheeseListingOutput;
use App\Filter\CheeseSearchFilter;
use App\Validator\IsValidPublished;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CheeseListingRepository;
use DateTimeInterface;
use DateTimeImmutable;

/**
 * @ORM\Entity(repositoryClass=CheeseListingRepository::class)
 * @ORM\Table(name="cheese_listing")
 * @IsValidPublished()
 */
#[ApiResource(
    collectionOperations: [
        'get',
        'post' => [
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' => [
                    ['cookieAuth' => []]
                ]
            ]
        ]
    ],
    itemOperations: [
        'get',
        'put' => [
            'security' => 'is_granted("CHEESE_EDIT", object)',
            'security_message' => 'Only author can edit this cheese listing',
            'openapi_context' => [
                'security' => [
                    ['cookieAuth' => []]
                ]
            ]
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_ADMIN")',
            'openapi_context' => [
                'security' => [
                    ['cookieAuth' => []]
                ]
            ]
        ]
    ],
    shortName: 'cheese',
    attributes: [
        'pagination_items_per_page' => 10,
        'formats' => ['jsonld', 'json', 'html', 'jsonhal', 'csv' => ['text/csv']]
    ],
    input: CheeseListingInput::class,
    output: CheeseListingOutput::class
)]
#[ApiFilter(BooleanFilter::class, properties: ['isPublished'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'title' => 'partial',
        'owner' => 'exact',
        'owner.username' => 'partial'
    ]
)]
#[ApiFilter(RangeFilter::class, properties: ['price'])]
#[ApiFilter(PropertyFilter::class)]
#[ApiFilter(CheeseSearchFilter::class)]
class CheeseListing
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private ?string $title = null;

    /**
     * @ORM\Column(type="text")
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="integer")
     */
    private ?int $price = null;

    /**
     * @ORM\Column(type="datetime")
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isPublished;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="cheeseListings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $owner;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isPublished = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setPrice(?int $price): self
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

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;
        return $this;
    }

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): self
    {
        $this->owner = $owner;

        return $this;
    }
}