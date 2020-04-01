<?php


namespace App\Security;

use App\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;

class EntityUserProvider extends \HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider
{

	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$resourceOwnerName = $response->getResourceOwner()->getName();

		if (!isset($this->properties[$resourceOwnerName]))
		{
			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
		}

		$serviceName = $response->getResourceOwner()->getName();
		$setter = 'set' . ucfirst($serviceName);
		$setterId = $setter . 'Id';
		$setterAccessToken = $setter . 'AccessToken';

		$username = $response->getUsername();
		if (null === $user = $this->findUser([$this->properties[$resourceOwnerName] => $username]))
		{
			$user = new User();
			$user
				->setEmail($response->getEmail())
				->$setterId($username)
				->setNickname($response->getNickname())
				->setRoles([User::ROLE_USER_CONFIRMED])
				->setActive(true)
				->$setterAccessToken($response->getAccessToken());

			$this->em->persist($user);
			$this->em->flush();

			return $user;
		}

		$user->setGithubAccessToken($response->getAccessToken());

		return $user;
	}

}
