<?php


namespace App\Security\Voter;

use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class PostVoter
 * @package App\Security\Voter
 */
class PostVoter extends Voter
{
	public const EDIT = 'edit';

	/**
	 * @inheritDoc
	 */
	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Post && $attribute === self::EDIT;
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

		return $this->canEdit($subject, $user);
	}

	/**
	 * @param Post $post
	 * @param User $user
	 *
	 * @return bool
	 */
	private function canEdit(Post $post, User $user): bool
	{
		return $user === $post->getAuthor();
	}
}
