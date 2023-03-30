<?php

declare(strict_types=1);

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Enum\MailTemplate;
use App\Services\SendInBlueMailNotifier;

final class CreateUserProcessor implements ProcessorInterface
{
    private bool $isMailingEnabled = false;

    public function __construct(
        private ProcessorInterface $persistProcessor,
        private SendInBlueMailNotifier $mailNotifier
    ) {
    }

    /**
     * @param array<array-key, mixed> $uriVariables
     * @param array<array-key, mixed> $context
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($operation instanceof Post === false) {
            throw new \LogicException('This processor must only handle POST request');
        }

        $result = $this->persistProcessor->process($data, $operation, $uriVariables, $context);

        if ($this->isMailingEnabled) {
            $this->sendWelcomeEmail($data);
            $this->sendAccountVerificationMail($data);
        }

        return $result;
    }

    private function sendWelcomeEmail(User $user): void
    {
        try {
            $this->mailNotifier->mail('test', MailTemplate::ACCOUNT_VALIDATION, 'user.email@email.com', []);
        } catch (\Exception) {
        } // Silent exception as we do not want to break user creation
    }

    private function sendAccountVerificationMail(User $user): void
    {
        try {
            $this->mailNotifier->mail('test', MailTemplate::ACCOUNT_VALIDATION, 'user.email@email.com', []);
        } catch (\Exception) {
        } // Silent exception as we do not want to break user creation
    }
}
