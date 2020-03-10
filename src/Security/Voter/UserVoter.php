<?php


namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class UserVoter
 * @package App\Security\Voter
 */
class UserVoter extends Voter
{
	public const EDIT = 'edit';

	/**
	 * @inheritDoc
	 */
	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof User && $attribute === self::EDIT;
	}

	/**
	 * @inheritDoc
	 */
	protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
	{
		$user = $token->getUser();

		if (!$user instanceof User)
		{
			return false;
		}

		return $user === $subject;
	}
}
