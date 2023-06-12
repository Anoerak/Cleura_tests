<?php

namespace App\MessageHandler;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\AccountConfirmationLinkService;
use App\Message\SendConfirmationAccountLinkMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'async')]
final class SendConfirmationAccountLinkMessageHandler
{

    public function __construct(private AccountConfirmationLinkService $accountConfirmationLinkService, private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(SendConfirmationAccountLinkMessage $message)
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['id' => $message->getUserId()]);

        $this->accountConfirmationLinkService->sendConfirmationLink($user);
    }
}
