<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Services\ConfirmationEmail;
use App\Services\FileUploader;
use App\Twig\BootstrapColorExtension;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
	/**
	 * @Route("/register", name="app_register")
	 * @param Request                      $request
	 * @param UserPasswordEncoderInterface $passwordEncoder
	 * @param GuardAuthenticatorHandler    $guardHandler
	 * @param LoginFormAuthenticator       $authenticator
	 * @param TokenGeneratorInterface      $generator
	 * @param ConfirmationEmail            $email
	 * @param FileUploader                 $fileUploader
	 * @param string                       $userAvatarsDirectory
	 *
	 * @return Response
	 * @throws TransportExceptionInterface
	 * @throws Exception
	 */
	public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, GuardAuthenticatorHandler $guardHandler, LoginFormAuthenticator $authenticator, TokenGeneratorInterface $generator, ConfirmationEmail $email, FileUploader $fileUploader, string $userAvatarsDirectory): Response
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

			$avatar = $form->get('avatar')->getData();
			if ($avatar)
			{
				$imageFileName = $fileUploader->upload($avatar, 'user_avatars_directory');
				$user->setAvatar($imageFileName);
			}

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
	 * @param UserRepository      $userRepository
	 * @param string              $token
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 */
	public function confirmation(UserRepository $userRepository, string $token, TranslatorInterface $translator): RedirectResponse
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

			$this->addFlash('success', sprintf($translator->trans('app.auth.messages.confirmation.success'), '<i class="far fa-thumbs-up"></i>'));
		}
		else
		{
			$this->addFlash('danger', $translator->trans('app.auth.messages.confirmation.error'));
		}


		return $this->redirectToRoute('blog_index');
	}

	/**
	 * @Route("/resend-confiramtion", name="app_resend_confirmation")
	 * @param ConfirmationEmail   $email
	 *
	 * @param TranslatorInterface $translator
	 *
	 * @return RedirectResponse
	 * @throws TransportExceptionInterface
	 */
	public function resendConfirmation(ConfirmationEmail $email, TranslatorInterface $translator): RedirectResponse
	{
		$user = $this->getUser();
		if ($email->send($user))
		{
			$this->addFlash('success', $translator->trans('app.auth.messages.resend_confirmation.success'));
		}
		else
		{
			$this->addFlash('danger', $translator->trans('app.auth.messages.resend_confirmation.error'));
		}

		return $this->redirectToRoute('blog_index');
	}

}
