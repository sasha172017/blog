<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Stores the locale of the user in the session after the
 * login. This can be used by the LocaleSubscriber afterwards.
 */
class UserLocaleSubscriber implements EventSubscriberInterface
{
	private $session;

	private $defaultLocale;

	public function __construct(SessionInterface $session, string $defaultLocale)
	{
		$this->session = $session;
		$this->defaultLocale = $defaultLocale;
	}

	public function onInteractiveLogin(InteractiveLoginEvent $event): void
	{
		$user = $event->getAuthenticationToken()->getUser();

		$this->session->set('_locale', $user->getLocale() ?? $this->defaultLocale);
	}

	public static function getSubscribedEvents()
	{
		return [
			SecurityEvents::INTERACTIVE_LOGIN => 'onInteractiveLogin',
		];
	}
}