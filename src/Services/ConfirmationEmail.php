<?php


namespace App\Services;

use App\Entity\User;
use Exception;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ConfirmationEmail
{
	/**
	 * @var MailerInterface
	 */
	private $mailer;

	public function __construct(MailerInterface $mailer)
	{
		$this->mailer = $mailer;
	}

	/**
	 * @param User $user
	 *
	 * @return bool
	 * @throws TransportExceptionInterface
	 */
	public function send(User $user): bool
	{
		try
		{
			$email = (new TemplatedEmail())
				->from($_ENV['MAILER_FROM'])
				->to($user->getEmail())
				->subject('Hello confirm your account!')
				->htmlTemplate('emails/confirmation_user.html.twig')
				->context(['user' => $user]);

			$this->mailer->send($email);
		}
		catch (Exception $e)
		{
			return false;
		}

		return true;
	}
}
