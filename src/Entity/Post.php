<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="post")
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:post:collection']]
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write:post']]
        ]
    ],
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:post:collection', 'read:post:item', 'read:user:collection']]
        ],
        'put' => [
            'denormalization_context' => ['groups' => ['write:post']]
        ],
        'delete'
    ]
)]
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:post:collection'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:post:collection', 'write:post'])]
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:post:collection', 'write:post'])]
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:post:collection', 'write:post'])]
    private ?string $summary = null;

    /**
     * @ORM\Column(type="text")
     */
    #[Groups(['read:post:item', 'write:post'])]
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:post:item'])]
    private \DateTime $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts")
     */
    #[Groups(['read:post:item', 'write:post'])]
    private ?User $author = null;

    public function __construct()
    {
        $this->publishedAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPublishedAt(): \DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(User $author): self
    {
        $this->author = $author;

        return $this;
    }
}
