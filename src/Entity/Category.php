<?php

namespace App\Entity;

use App\Helpers\Timestamps;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity(fields={"title"}, message="Category arledy exist")
 * @ORM\HasLifecycleCallbacks
 */
class Category
{
	use Timestamps;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\Column(type="string", length=50, unique=true)
	 */
	private $title;

	/**
	 * @ORM\Column(type="string", length=100)
	 */
	private $slug;

	/**
	 * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="categories")
	 * @ORM\OrderBy({"updatedAt" = "DESC"})
	 */
	private $posts;

	/**
	 * @ORM\Column(type="integer", nullable=true)
	 */
	private $color;

	public function __construct()
	{
		$this->posts = new ArrayCollection();
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
			$post->addCategory($this);
		}

		return $this;
	}

	public function removePost(Post $post): self
	{
		if ($this->posts->contains($post))
		{
			$this->posts->removeElement($post);
			$post->removeCategory($this);
		}

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
