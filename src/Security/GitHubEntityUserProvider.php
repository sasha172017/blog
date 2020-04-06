<?php


namespace App\Security;

use App\Entity\User;
use App\Security\Exception\ExistEmailException;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\OAuthAwareUserProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class GitHubEntityUserProvider implements OAuthAwareUserProviderInterface, UserProviderInterface
{

	public const SESSION_DATA_KEY = 'github_data';

	/**
	 * @var ObjectManager
	 */
	protected $em;

	/**
	 * @var string
	 */
	protected $class;

	/**
	 * @var ObjectRepository
	 */
	protected $repository;

	/**
	 * @var array
	 */
	protected $properties = [
		'identifier' => 'id',
	];

	/**
	 * @var ValidatorInterface
	 */
	private $validator;

	/**
	 * @var Router
	 */
	private $router;
	/**
	 * @var Session
	 */
	private $session;

	/**
	 * Constructor.
	 *
	 * @param ManagerRegistry    $registry    manager registry
	 * @param ValidatorInterface $validator
	 * @param Router             $router
	 * @param Session            $session
	 * @param string             $class       user entity class to load
	 * @param array              $properties  Mapping of resource owners to properties
	 * @param string             $managerName Optional name of the entity manager to use
	 */
	public function __construct(ManagerRegistry $registry, ValidatorInterface $validator, Router $router, Session $session, $class, $properties, $managerName = null)
	{
		$this->em         = $registry->getManager($managerName);
		$this->class      = $class;
		$this->properties = array_merge($this->properties, $properties);
		$this->validator  = $validator;
		$this->router     = $router;
		$this->session = $session;
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByUsername($username)
	{
		$user = $this->findUser(['username' => $username]);
		if (!$user)
		{
			$exception = new UsernameNotFoundException(sprintf("User '%s' not found.", $username));
			$exception->setUsername($username);

			throw $exception;
		}

		return $user;
	}

	/**
	 * @param User $user
	 */
	private function validateUser(User $user)
	{
		$errors = $this->validator->validate($user);

		if ($errors->count() > 0)
		{
			if ($errors->get(0)->getPropertyPath() === 'email')
			{
				throw new ExistEmailException($errors->get(0)->getMessage());
			}

			throw new AuthenticationException($errors->get(0)->getMessage());
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$resourceOwnerName = $response->getResourceOwner()->getName();

		if (!isset($this->properties[$resourceOwnerName]))
		{
			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
		}

		$username = $response->getUsername();
		if (null === $user = $this->findUser([$this->properties[$resourceOwnerName] => $username]))
		{
			$user = new User();
			$user
				->setEmail($response->getEmail())
				->setGithubId($username)
				->setNickname($username)
				->setActive(true)
				->setRoles([User::ROLE_USER_CONFIRMED])
				->setGithubAccessToken($response->getAccessToken());

			$this->em->persist($user);
			$this->validateUser($user);
			$this->em->flush();

			$this->session->set(self::SESSION_DATA_KEY, $response->getData());

			return $user;
		}

		return $user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function refreshUser(UserInterface $user)
	{
		$accessor   = PropertyAccess::createPropertyAccessor();
		$identifier = $this->properties['identifier'];
		if (!$accessor->isReadable($user, $identifier) || !$this->supportsClass(\get_class($user)))
		{
			throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
		}

		$userId   = $accessor->getValue($user, $identifier);
		$username = $user->getUsername();

		if (null === $user = $this->findUser([$identifier => $userId]))
		{
			$exception = new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $userId));
			$exception->setUsername($username);

			throw $exception;
		}

		return $user;
	}

	/**
	 * {@inheritdoc}
	 */
	public function supportsClass($class)
	{
		return $class === $this->class || is_subclass_of($class, $this->class);
	}

	/**
	 * @param array $criteria
	 *
	 * @return object
	 */
	protected function findUser(array $criteria)
	{
		if (null === $this->repository)
		{
			$this->repository = $this->em->getRepository($this->class);
		}

		return $this->repository->findOneBy($criteria);
	}
}
