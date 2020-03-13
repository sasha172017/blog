<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Helpers\Timestamps;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * )
 * @ORM\Entity(repositoryClass="App\Repository\CommentRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Comment
{
	use Timestamps;

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $author;

	/**
	 * @ORM\ManyToOne(targetEntity="App\Entity\Post", inversedBy="comments")
	 * @ORM\JoinColumn(nullable=false)
	 */
	private $post;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private $content;

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

	public function getPost(): ?Post
	{
		return $this->post;
	}

	public function setPost(?Post $post): self
	{
		$this->post = $post;

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
}
