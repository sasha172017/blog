<?php

namespace App\Entity;

use App\Helpers\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"nickname"}, message="There is already an account with this nickname")
 * @ORM\HasLifecycleCallbacks
 */
class User implements UserInterface
{
	use Timestamps;

	public const ROLE_USER = 'ROLE_USER';

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=180, unique=true)
	 */
	private $email;

	/**
	 * @ORM\Column(type="json")
	 */
	private $roles = [];

	/**
	 * @var string The hashed password
	 * @ORM\Column(type="string")
	 */
	private $password;

	/**
	 * @ORM\Column(type="string", length=50, unique=true)
	 */
	private $nickname;

	/**
	 * @ORM\Column(type="date", nullable=true)
	 */
	private $dateOfBirth;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Post", mappedBy="author")
	 * @ORM\OrderBy({"updatedAt" = "DESC"})
	 */
	private $posts;

	/**
	 * @ORM\Column(type="string", length=150, nullable=true)
	 */
	private $verificationToken;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private $active;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $color;

	public function __construct()
         	{
         		$this->posts  = new ArrayCollection();
         		$this->active = false;
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
	public function getUsername(): string
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
         		$roles[] = self::ROLE_USER;
         
         		return array_unique($roles);
         	}

	public function setRoles(array $roles): self
         	{
         		$this->roles = $roles;
         
         		return $this;
         	}

	/**
	 * @see UserInterface
	 */
	public function getPassword(): string
         	{
         		return (string) $this->password;
         	}

	public function setPassword(string $password): self
         	{
         		$this->password = $password;
         
         		return $this;
         	}

	/**
	 * @see UserInterface
	 */
	public function getSalt()
         	{
         		// not needed when using the "bcrypt" algorithm in security.yaml
         	}

	/**
	 * @see UserInterface
	 */
	public function eraseCredentials()
         	{
         		// If you store any temporary, sensitive data on the user, clear it here
         		// $this->plainPassword = null;
         	}

	public function getNickname(): ?string
         	{
         		return $this->nickname;
         	}

	public function setNickname(string $nickname): self
         	{
         		$this->nickname = $nickname;
         
         		return $this;
         	}

	public function getDateOfBirth(): ?\DateTimeInterface
         	{
         		return $this->dateOfBirth;
         	}

	public function setDateOfBirth(?\DateTimeInterface $dateOfBirth): self
         	{
         		$this->dateOfBirth = $dateOfBirth;
         
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
         		if (!$this->posts->contains($post))
         		{
         			$this->posts[] = $post;
         			$post->setAuthor($this);
         		}
         
         		return $this;
         	}

	public function removePost(Post $post): self
         	{
         		if ($this->posts->contains($post))
         		{
         			$this->posts->removeElement($post);
         			// set the owning side to null (unless already changed)
         			if ($post->getAuthor() === $this)
         			{
         				$post->setAuthor(null);
         			}
         		}
         
         		return $this;
         	}

	public function getVerificationToken(): ?string
         	{
         		return $this->verificationToken;
         	}

	public function setVerificationToken(?string $verificationToken): self
         	{
         		$this->verificationToken = $verificationToken;
         
         		return $this;
         	}

	public function getActive(): ?bool
         	{
         		return $this->active;
         	}

	public function setActive(bool $active): self
         	{
         		$this->active = $active;
         
         		return $this;
         	}

    public function getColor(): ?int
    {
        return $this->color;
    }

    public function setColor(?int $color): self
    {
        $this->color = $color;

        return $this;
    }
}