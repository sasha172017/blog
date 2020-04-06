<?php


namespace App\Security\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

class ExistEmailException extends AuthenticationException
{
	/**
	 * {@inheritdoc}
	 */
	public function getMessageKey()
	{
		return 'There is already an account with this email';
	}
}
