<?php


namespace App\Helpers;

/**
 * Trait Timestamps
 * @package App\Helpers
 */
trait Timestamps
{
	/**
	 * @ORM\PrePersist
	 * @ORM\PreUpdate
	 */
	public function timestamps(): void
	{
		if ($this->getUpdatedAt() === null)
		{
			$this->setUpdatedAt((new \DateTime('now'))->getTimestamp());
		}

		if ($this->getCreatedAt() === null)
		{
			$this->setCreatedAt((new \DateTime('now'))->getTimestamp());
		}
	}
}
