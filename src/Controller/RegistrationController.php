<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\ConfirmationEmail;
use App\Twig\BootstrapColorExtension;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;

class RegistrationController extends AbstractController
{
	/**
	 * @Route("/register", name="app_register")
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param GuardAuthenticatorHandler    $guardHandler
	 * @param LoginFormAuthenticator       $authenticator
	 *
	 * @param TokenGeneratorInterface      $generator
	 *
	 * @param ConfirmationEmail            $email
	 *
	 * @return Response
	 * @throws \Exception
	 * @throws TransportExceptionInterface
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TokenGeneratorInterface $generator, ConfirmationEmail $email): Response
	{
		$user = new User();
		$form = $this->createForm(RegistrationFormType::class, $user);
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid())
		{
			// encode the plain password
			$user->setPassword(
				$passwordEncoder->encodePassword(
					$user,
					$form->get('plainPassword')->getData()
				)
			);

			$user
				->setVerificationToken($generator->generateToken())
				->setColor(random_int(0, count(BootstrapColorExtension::COLORS_CLASS) - 1));

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			// confirmation account
			$email->send($user);

			return $guardHandler->authenticateUserAndHandleSuccess(
				$user,
				$request,
				$authenticator,
				'main' // firewall name in security.yaml
			);
		}

		return $this->render('registration/register.html.twig', [
			'registrationForm' => $form->createView(),
		]);
	}

	/**
	 * @Route("/confirmation/{token}", name="app_confirmation_register")
	 * @param UserRepository $userRepository
	 * @param string         $token
	 *
	 * @return RedirectResponse
	 */
	public function confirmation(UserRepository $userRepository, string $token): RedirectResponse
	{
		$user = $userRepository->findOneBy(['active' => false, 'verificationToken' => $token]);
		if ($user !== null)
		{
			$user
				->setActive(true)
				->setRoles([User::ROLE_USER_CONFIRMED]);

			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist($user);
			$entityManager->flush();

			$this->addFlash('success', sprintf('%s Congratulations, your account is activated!', '<i class="far fa-thumbs-up"></i>'));
		}
		else
		{
			$this->addFlash('danger', 'Failed to activate a profile!');
		}


		return $this->redirectToRoute('blog_index');
	}

	/**
	 * @Route("/resend-confiramtion", name="app_resend_confirmation")
	 * @param ConfirmationEmail $email
	 *
	 * @return RedirectResponse
	 * @throws TransportExceptionInterface
	 */
	public function resendConfirmation(ConfirmationEmail $email): RedirectResponse
	{
		$user = $this->getUser();
		$email->send($user);

		return $this->redirectToRoute('blog_index');
	}

}
