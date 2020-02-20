<?php


namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BookmarksVoter extends Voter
{

	public const SHOW = 'show';

	/**
	 * @inheritDoc
	 */
	protected function supports(string $attribute, $subject)
	{
		return $subject instanceof User && $attribute === self::SHOW;
	}

	/**
	 * @inheritDoc
	 */
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token)
	{
		$user = $token->getUser();

		if (!$user instanceof User)
		{
			return false;
		}

		return $this->canShow($subject, $user);
	}

	/**
	 * @param User $subject
	 * @param User $user
	 *
	 * @return bool
	 */
	private function canShow(User $subject, User $user): bool
	{
		return $user === $subject;
	}

}
