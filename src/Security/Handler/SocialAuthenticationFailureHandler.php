<?php

namespace App\Security\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;

/**
 * Class SocialAuthenticationFailureHandler
 * @package App\Security\Handler
 */
class SocialAuthenticationFailureHandler extends DefaultAuthenticationFailureHandler
{
	/**
	 * @param Request                 $request
	 * @param AuthenticationException $exception
	 *
	 * @return RedirectResponse|Response
	 */
	public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
	{
		$this->options['failure_path'] = '/' . $request->getLocale() . $this->options['login_path'];

		return parent::onAuthenticationFailure($request, $exception);
	}
}
