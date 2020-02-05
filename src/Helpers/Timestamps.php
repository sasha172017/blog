<?php


namespace App\Helpers;

/**
 * Trait Timestamps
 * @package App\Helpers
 */
trait Timestamps
{
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

	/**
	 * @ORM\PrePersist
	 */
	public function onPrePersist(): void
	{
		if (empty($this->createdAt))
		{
			$this->setCreatedAt((new \DateTime('now'))->getTimestamp());
		}
	}

	/**
	 * @ORM\PreFlush
	 */
	public function onPreFlush(): void
	{
		if (empty($this->updatedAt))
		{
			$this->setUpdatedAt((new \DateTime('now'))->getTimestamp());
		}
	}

}
