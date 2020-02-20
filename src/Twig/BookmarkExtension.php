<?php

namespace App\Twig;

use App\Entity\Post;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\NonUniqueResultException;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BookmarkExtension extends AbstractExtension
{
	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * BookmarkExtension constructor.
	 *
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function getFunctions(): array
	{
		return [
			new TwigFunction('in_bookmark', [$this, 'inBookmark']),
		];
	}

	/**
	 * @param User $user
	 * @param Post $post
	 *
	 * @return bool
	 * @throws NonUniqueResultException
	 */
	public function inBookmark(User $user, Post $post): bool
	{
		return $this->userRepository->inBookmark($user->getId(), $post->getId());
	}
}
