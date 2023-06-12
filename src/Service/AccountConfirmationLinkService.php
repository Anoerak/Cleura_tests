<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class AccountConfirmationLinkService
{
	public function __construct(private MailerInterface $mailer, private UserRepository $userRepository, private VerifyEmailHelperInterface $verifyEmailHelper,)
	{
	}

	public function sendConfirmationLink(User $user): void
	{
		/*
		|--------------------------------------------
		| We generate the confirmation URL.
		|--------------------------------------------
		*/
		$signatureComponents = $this->verifyEmailHelper->generateSignature(
			'app_verify_email',
			$user->getId(),
			$user->getEmail(),
			['id' => $user->getId()]
		);

		/*
		|--------------------------------------------
		| We send an email with a confirmation link.
		|--------------------------------------------
		*/
		$email = (new TemplatedEmail())
			->from(new Address('largowick@gmail.com', 'Cleura Daemon'))
			->to($user->getEmail())
			->subject('Please Confirm your Email')
			->htmlTemplate('emails/confirmation_email.html.twig')
			->context([
				'signedUrl' => $signatureComponents->getSignedUrl(),
				'expiresAtMessageKey' => $signatureComponents->getExpirationMessageKey(),
				'expiresAtMessageData' => $signatureComponents->getExpirationMessageData(),
			]);

		$this->mailer->send($email);
	}
}
