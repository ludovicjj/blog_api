<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use App\Controller\MeController;
use App\Repository\CheeseListingRepository;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
 * @UniqueEntity("username")
 */
#[ApiResource(
    collectionOperations: [
        'get'=> [
            'security' => 'is_granted("ROLE_USER")'
        ],
        'post' => [
            'security' => 'is_granted("PUBLIC_ACCESS")',
            'validation_groups' => ['Default', 'create']
        ],
        'me' => [
            'path' => '/me',
            'method' => 'get',
            'controller' => MeController::class,
            'read' => false,
            'pagination_enabled' => false,
            'filters' => [],
            'security' => 'is_granted("ROLE_USER")',
            'openapi_context' => [
                'security' => ['cookieAuth' => ['']]
            ]
        ]
    ],
    itemOperations: [
        'get' => [
            'security' => 'is_granted("ROLE_USER")'
        ],
        'put' => [
            'security' => 'is_granted("ROLE_USER") and object == user'
        ],
        'delete' => [
            'security' => 'is_granted("ROLE_ADMIN")'
        ]
    ],
    attributes: [
        'pagination_items_per_page' => 2
    ]
)]
#[ApiFilter(PropertyFilter::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    #[Groups(['user:read', 'user:write'])]
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Groups(['admin:write'])]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    #[Groups(['user:write'])]
    #[SerializedName('password')]
    #[Assert\NotBlank(groups: ['create'])]
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    #[Groups(['user:read', 'user:write', 'cheese:item:get'])]
    private $username;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    #[Groups(['admin:read', 'user:write', 'owner:read'])]
    private $phoneNumber;

    /**
     * Returns true if this is the currently-authenticated user
     */
    #[Groups(['user:read'])]
    private bool $isMe = false;

    /**
     * Returns true if this user is an MVP
     */
    #[Groups(['user:read'])]
    private $isMvp = false;

    /**
     * @ORM\OneToMany(targetEntity=CheeseListing::class, mappedBy="owner", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid()
     */
    #[Groups(['user:write'])]
    private $cheeseListings;

    public function __construct()
    {
        $this->cheeseListings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
         $this->plainPassword = null;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return Collection|CheeseListing[]
     */
    public function getCheeseListings(): Collection
    {
        return $this->cheeseListings;
    }

    /**
     * @return Collection<CheeseListing>
     */
    #[Groups(['user:read'])]
    #[SerializedName('cheeseListings')]
    public function getPublishedCheeseListings(): Collection
    {
        return $this->cheeseListings->matching(CheeseListingRepository::createPublishedCriteria());
    }

    public function addCheeseListing(CheeseListing $cheeseListing): self
    {
        if (!$this->cheeseListings->contains($cheeseListing)) {
            $this->cheeseListings[] = $cheeseListing;
            $cheeseListing->setOwner($this);
        }

        return $this;
    }

    public function removeCheeseListing(CheeseListing $cheeseListing): self
    {
        if ($this->cheeseListings->removeElement($cheeseListing)) {
            // set the owning side to null (unless already changed)
            if ($cheeseListing->getOwner() === $this) {
                $cheeseListing->setOwner(null);
            }
        }

        return $this;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    public function setPhoneNumber(?string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;
        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function getIsMe(): bool
    {
        return $this->isMe;
    }


    public function setIsMe(bool $isMe): self
    {
        $this->isMe = $isMe;
        return $this;
    }

    public function getIsMvp(): bool
    {
        return $this->isMvp;
    }

    public function setIsMvp(bool $isMvp)
    {
        $this->isMvp = $isMvp;
    }
}
