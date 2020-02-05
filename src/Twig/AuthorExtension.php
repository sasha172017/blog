<?php

namespace App\Twig;

use Symfony\Component\Security\Core\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AuthorExtension extends AbstractExtension
{
	private $security;

	/**
	 * AuthorExtension constructor.
	 *
	 * @param Security $security
	 */
	public function __construct(Security $security)
	{
		$this->security = $security;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('is_author', [$this, 'isAuthor']),
		];
	}

	/**
	 * @param $subject
	 *
	 * @return bool
	 */
	public function isAuthor($subject): bool
	{
		return $subject->getAuthor() === $this->security->getUser();
	}
}
