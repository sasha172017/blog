<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class UrlRemember
 * @package App\Services
 */
class UrlRemember
{
	private $defaultRoute;
	private $requestStack;
	private $router;
	private $session;

	public function __construct(string $defaultRoute, RequestStack $requestStack, UrlGeneratorInterface $router, SessionInterface $session)
	{
		$this->defaultRoute = $defaultRoute;
		$this->requestStack = $requestStack;
		$this->router       = $router;
		$this->session      = $session;
	}

	/**
	 * @param string $key
	 */
	public function remember(string $key = self::class): void
	{
		if (($request = $this->requestStack->getCurrentRequest()) !== null)
		{
			$this->session->set($key, $request->getUri());
		}
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function previous(string $key = self::class): string
	{
		return $this->session->get($key) ?? $this->router->generate($this->defaultRoute);
	}

}
