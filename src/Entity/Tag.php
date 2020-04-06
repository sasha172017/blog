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
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"tag:read"}},
 *     denormalizationContext={"groups"={"tag:write"}},
 *     collectionOperations={
 *         "get",
 *         "post"={"security"="is_granted('ROLE_ADMIN')"}
 *     },
 *     itemOperations={
 *         "get",
 *         "put"={"security"="is_granted('ROLE_ADMIN')"},
 *         "delete"={"security"="is_granted('ROLE_ADMIN')"},
 *     }
 * )
 * @ApiFilter(DateFilter::class, properties={"updatedAt"})
 * @ApiFilter(OrderFilter::class, properties={"title", "slug", "color", "createdAt", "updatedAt"})
 * @ApiFilter(SearchFilter::class, properties={"color": "exact", "title": "partial", "slug" : "partial"})
 * @ApiFilter(PropertyFilter::class)
 *
 * @ORM\Entity(repositoryClass="App\Repository\TagRepository")
 * @UniqueEntity(fields={"title"}, message="Tag arledy exist")
 * @ORM\HasLifecycleCallbacks
 */
class Tag
{
	//use Timestamps;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @Groups({"tag:read", "tag:write"})
	 * @Assert\NotBlank
	 * @ORM\Column(type="string", length=50, unique=true)
	 */
	private $title;

	/**
	 * @Groups({"tag:read", "tag:write"})
	 * @ORM\Column(type="string", length=100)
	 */
	private $slug;

	/**
	 * @Groups({"tag:read", "tag:write"})
	 * @ORM\ManyToMany(targetEntity="App\Entity\Post", mappedBy="tags")
	 */
	private $posts;

	/**
	 * @Groups({"tag:read", "tag:write"})
	 * @ORM\Column(type="integer", options={"default":0})
	 */
	private $color;

	/**
	 * @Groups({"tag:read"})
	 * @ORM\Column(type="datetime")
	 */
	private $createdAt;

	/**
	 * @Groups({"tag:read", "tag:write"})
	 * @ORM\Column(type="datetime")
	 */
	private $updatedAt;

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
			$post->addTag($this);
		}

		return $this;
	}

	public function removePost(Post $post): self
	{
		if ($this->posts->contains($post))
		{
			$this->posts->removeElement($post);
			$post->removeTag($this);
		}

		return $this;
	}

	public function getColor(): ?int
	{
		return $this->color;
	}

	public function setColor(int $color = 0): self
	{
		$this->color = $color;

		return $this;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(\DateTimeInterface $createdAt): self
	{
		$this->createdAt = $createdAt;

		return $this;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(\DateTimeInterface $updatedAt): self
	{
		$this->updatedAt = $updatedAt;

		return $this;
	}

	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 * @throws \Exception
	 */
	public function updatedTimestamps(): void
	{
		if ($this->getUpdatedAt() === null)
		{
			$this->setUpdatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
		}

		if ($this->getCreatedAt() === null)
		{
			$this->setCreatedAt(new \DateTime('now', new \DateTimeZone('UTC')));
		}
	}
}
