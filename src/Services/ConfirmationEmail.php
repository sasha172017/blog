<?php


namespace App\Services;

use App\Entity\User;
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
	 * @throws TransportExceptionInterface
	 */
	public function send(User $user): void
	{
		$email = (new TemplatedEmail())
			->from($_ENV['MAILER_FROM'])
			->to($user->getEmail())
			->subject('Hello confirm your account!')
			->htmlTemplate('emails/confirmation_user.html.twig')
			->context(['user' => $user]);

		$this->mailer->send($email);
	}
}
