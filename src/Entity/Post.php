<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use App\Controller\PostCountController;
use App\Filter\MySearchFilter;
use App\Repository\PostRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use DateTime;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="post")
 * @UniqueEntity(
 *     fields={"slug"},
 *     errorPath="title",
 *     message="Ce titre est déjà utilisé dans un autre article."
 * )
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['read:post:collection']]
        ],
        'post' => [
            'denormalization_context' => ['groups' => ['write:post']]
        ],
        'count' => [
            'method' => 'GET',
            'path' => '/post/count',
            'controller' => PostCountController::class,
            'filters' => [],
            'pagination_enabled' => false,
            'openapi_context' => [
                'summary' => 'Permet de récupérer le nombre total d\'articles',
                'description' => 'Permet de récupérer le nombre total d\'articles',
                'parameters' => [
                    [
                        'in' => 'query',
                        'name' => 'published',
                        'required' => false,
                        'schema' => [
                            'type' => 'integer',
                            'maximum' => 1,
                            'minimum' => 0,
                            'example' => '1'
                        ],
                        'description' => 'Filtre les articles publiés'
                    ]
                ],
                'responses' => [
                    '200' => [
                        'description' => 'total articles',
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'properties' => [
                                        'posts' => [
                                            'type' => 'integer',
                                            'example' => 15
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
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
    ],
    paginationClientItemsPerPage: true,
    paginationItemsPerPage: 2,
    paginationMaximumItemsPerPage: 10
)]
#[ApiFilter(SearchFilter::class, properties: ['author.username' => 'exact'])]
#[ApiFilter(MySearchFilter::class, properties: ['title', 'summary'])]
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
    #[
        Groups(['read:post:collection', 'write:post']),
        Assert\NotBlank(message: 'Le champs titre est obligatoire.'),
        Assert\Length(
            min: 3,
            max: 255,
            minMessage: 'Le titre doit comporter minimum {{ limit }} caractères.',
            maxMessage: 'Le titre ne doit pas comporter plus de {{ limit }} caractères.'
        )
    ]
    private ?string $title = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:post:collection', 'write:post']),
        Assert\NotBlank(message: 'Le champs slug est obligatoire.'),
        Assert\Length(max: 255)
    ]
    private ?string $slug = null;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[
        Groups(['read:post:collection', 'write:post']),
        Assert\Length(
            max: 255,
            maxMessage: 'Le résumé ne doit pas comporter plus de {{ limit }} caractères.'
        )
    ]
    private ?string $summary = null;

    /**
     * @ORM\Column(type="text")
     */
    #[
        Groups(['read:post:item', 'write:post']),
        Assert\NotBlank(message: "L'article doit avoir un contenu."),
        Assert\Length(
            min: 10,
            minMessage: "Le contenu de l'article doit comporter minimum {{ limit }} caractères."
        )
    ]
    private ?string $content = null;

    /**
     * @ORM\Column(type="datetime")
     */
    #[Groups(['read:post:item'])]
    private DateTime $publishedAt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="posts", cascade={"persist"})
     */
    #[
        Groups(['read:post:item', 'write:post']),
        Assert\Valid
    ]
    private ?User $author = null;

    /**
     * @ORM\Column(type="boolean", options={"default" : 0})
     */
    private bool $isPublished;

    public function __construct()
    {
        $this->publishedAt = new DateTime();
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

    public function getPublishedAt(): DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTime $publishedAt): self
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

    public function getIsPublished(): bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }
}
