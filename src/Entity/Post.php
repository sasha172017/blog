<?php

namespace App\Entity;

use App\Helpers\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
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
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $author;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $title;

	/**
	 * @ORM\Column(type="string", length=150)
	 */
	private $slug;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $summary;

	/**
	 * @ORM\Column(type="text")
	 */
	private $content;

	/**
	 * @ORM\Column(type="integer", options={"default":0})
	 */
	private $views;

	/**
	 * @ORM\Column(type="string", length=25, nullable=true)
	 */
	private $image;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Category", inversedBy="posts")
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 */
	private $categories;

	/**
	 * @ORM\OneToMany(targetEntity="App\Entity\Comment", mappedBy="post", orphanRemoval=true)
	 * @ORM\OrderBy({"createdAt" = "DESC"})
	 */
	private $comments;

	/**
	 * @ORM\Column(type="integer", options={"default":0})
	 */
	private $ratingUp;

	/**
	 * @ORM\Column(type="integer", options={"default":0})
	 */
	private $ratingDown;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\User", mappedBy="bookmarks")
	 */
	private $users;

	public function __construct()
	{
		$this->categories = new ArrayCollection();
		$this->comments   = new ArrayCollection();
		$this->users      = new ArrayCollection();
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
	 * @return Collection|Category[]
	 */
	public function getCategories(): Collection
	{
		return $this->categories;
	}

	public function addCategory(Category $category): self
	{
		if (!$this->categories->contains($category))
		{
			$this->categories[] = $category;
		}

		return $this;
	}

	public function removeCategory(Category $category): self
	{
		if ($this->categories->contains($category))
		{
			$this->categories->removeElement($category);
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

}
