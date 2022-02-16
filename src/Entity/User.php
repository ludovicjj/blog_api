<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 */
#[ApiResource]
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:user:collection'])]
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Groups(['read:user:collection'])]
    private ?string $username = null;

    /**
     * @ORM\Column(type="string", unique=true)
     */
    #[Groups(['read:user:collection'])]
    private ?string $email = null;

    /**
     * @ORM\Column(type="string")
     */
    private ?string $password = null;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var Post[]|Collection
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="author")
     */
    private Collection $posts;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getRoles(): array
    {
        $roles =  $this->roles;
        if (empty($roles)) {
            $roles[] = 'ROLE_USER';
        }
        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * @return Collection|Post[]
     */
    public function getPosts(): Collection
    {
        return $this->posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setAuthor($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getAuthor() === $this) {
                $post->setAuthor(null);
            }
        }

        return $this;
    }
}