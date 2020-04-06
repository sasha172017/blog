<?php

namespace App\Security\Handler;

use App\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;

/**
 * Class SocialAuthenticationSuccessHandler
 * @package App\Security\Handler
 */
class SocialAuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler
{

	/**
	 * @param Request        $request
	 * @param TokenInterface $token
	 *
	 * @return RedirectResponse|Response
	 */
	public function onAuthenticationSuccess(Request $request, TokenInterface $token)
	{
		$this->options['default_target_path'] = '/' . $request->getLocale() . $this->options['default_target_path'];

		if (in_array(User::ROLE_SOCIAL_USER, $token->getUser()->getRoles()))
		{
			$this->options['default_target_path'] = '/';
		}

		return $this->httpUtils->createRedirectResponse($request, $this->determineTargetUrl($request));
	}

}
