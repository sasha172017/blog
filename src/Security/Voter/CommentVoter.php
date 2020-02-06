<?php


namespace App\Security\Voter;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class CommentVoter
 * @package App\Security\Voter
 */
class CommentVoter extends Voter
{
	public const EDIT = 'edit';
	public const DELETE = 'delete';

	/**
	 * @inheritDoc
	 */
	protected function supports(string $attribute, $subject): bool
	{
		return $subject instanceof Comment && in_array($attribute, [self::EDIT, self::DELETE], true);
	}

	/**
	 * @inheritDoc
	 */
	protected function voteOnAttribute(string $attribute, $comment, TokenInterface $token): bool
	{
		$user = $token->getUser();

		if (!$user instanceof User)
		{
			return false;
		}

		return $user === $comment->getAuthor();
	}
}
