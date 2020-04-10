<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use App\Helpers\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ApiResource(
 *     normalizationContext={"groups"={"post:read"}},
 *     denormalizationContext={"groups"={"post:write"}},
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_USER_CONFIRMED')"}
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('ROLE_USER_CONFIRMED') or object.getAuthor() == user"},
 *         "delete"={"security"="is_granted('ROLE_USER_CONFIRMED') or object.getAuthor() == user"},
 *     }
 * )
 * @ApiFilter(OrderFilter::class, properties={"author.nickname", "title", "slug", "summary", "views", "rating"})
 * @ApiFilter(SearchFilter::class, properties={"views": "exact", "rating": "exact", "title": "partial", "slug" : "partial"})
 * @ApiFilter(PropertyFilter::class)
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Post
{
	use Timestamps;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $author;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @Assert\NotBlank
	 * @ORM\Column(type="string", length=100)
	 */
	private $title;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\Column(type="string", length=150)
	 */
	private $slug;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @Assert\NotBlank
	 * @ORM\Column(type="string", length=255)
	 */
	private $summary;

	/**
	 * @Groups({"post:write"})
	 * @Assert\NotBlank
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\Column(type="integer", options={"default":0})
	 */
	private $views;

	/**
	 * @ORM\Column(type="string", length=50, nullable=true)
	 */
	private $image;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @Assert\NotBlank
	 * @ORM\ManyToMany(targetEntity="App\Entity\Tag", inversedBy="posts")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 */
	private $tags;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
	 * @ORM\OrderBy({"updatedAt" = "DESC"})
	 */
	private $comments;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\Column(type="integer", options={"default":0}, nullable=true)
	 */
	private $ratingUp;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\Column(type="integer", options={"default":0}, nullable=true)
	 */
	private $ratingDown;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\Column(type="integer", options={"default":0}, nullable=true)
	 */
	private $rating;

	/**
	 * @Groups({"post:read", "post:write"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="bookmarks")
	 */
	private $users;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $createdAt;

	/**
	 * @ORM\Column(type="integer")
	 */
	private $updatedAt;

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	public function setCreatedAt(int $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt()
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(int $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	public function __construct()
	{
		$this->tags     = new ArrayCollection();
		$this->comments = new ArrayCollection();
		$this->users    = new ArrayCollection();
	}

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getAuthor(): ?User
	{
		return $this->author;
	}

	public function setAuthor(?User $author): self
	{
		$this->author = $author;

		return $this;
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

	public function getViews(): ?int
	{
		return $this->views;
	}

	public function setViews(int $views = 0): self
	{
		$this->views = $views;

		return $this;
	}

	public function getImage(): ?string
	{
		return $this->image;
	}

	public function setImage(?string $image): self
	{
		$this->image = $image;

		return $this;
	}

	/**
	 * @return Collection|Tag[]
	 */
	public function getTags(): Collection
	{
		return $this->tags;
	}

	public function addTag(Tag $tag): self
	{
		if (!$this->tags->contains($tag))
		{
			$this->tags[] = $tag;
		}

		return $this;
	}

	public function removeTag(Tag $tag): self
	{
		if ($this->tags->contains($tag))
		{
			$this->tags->removeElement($tag);
		}

		return $this;
	}

	/**
	 * @return Collection|Comment[]
	 */
	public function getComments(): Collection
	{
		return $this->comments;
	}

	public function addComment(Comment $comment): self
	{
		if (!$this->comments->contains($comment))
		{
			$this->comments[] = $comment;
			$comment->setPost($this);
		}

		return $this;
	}

	public function removeComment(Comment $comment): self
	{
		if ($this->comments->contains($comment))
		{
			$this->comments->removeElement($comment);
			// set the owning side to null (unless already changed)
			if ($comment->getPost() === $this)
			{
				$comment->setPost(null);
			}
		}

		return $this;
	}

	public function getRatingUp(): ?int
	{
		return $this->ratingUp;
	}

	public function setRatingUp(int $ratingUp = 0): self
	{
		$this->ratingUp = $ratingUp;

		return $this;
	}

	public function getRatingDown(): ?int
	{
		return $this->ratingDown;
	}

	public function setRatingDown(int $ratingDown = 0): self
	{
		$this->ratingDown = $ratingDown;

		return $this;
	}

	/**
	 * @return Collection|User[]
	 */
	public function getUsers(): Collection
	{
		return $this->users;
	}

	public function addUser(User $user): self
	{
		if (!$this->users->contains($user))
		{
			$this->users[] = $user;
			$user->addBookmark($this);
		}

		return $this;
	}

	public function removeUser(User $user): self
	{
		if ($this->users->contains($user))
		{
			$this->users->removeElement($user);
			$user->removeBookmark($this);
		}

		return $this;
	}

	public function getRating(): ?int
	{
		return $this->rating;
	}

	public function setRating(int $rating = 0): self
	{
		$this->rating = $rating;

		return $this;
	}

	/**
	 * @ORM\PreFlush
	 */
	public function onPreFlush(): void
	{
		$this->setRating($this->getRatingUp() - $this->getRatingDown());
	}

}
