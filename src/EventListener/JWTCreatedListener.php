<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class JWTCreatedListener
{
    public function __construct(
        private RequestStack $requestStack,
        private UserRepository $userRepository
    ) {
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $payload = $event->getData();

        $payload = $this->setIpAddress($payload);

        $payload = $this->setUserInformations($payload);

        $event->setData($payload);
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function setIpAddress(array $payload): array
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $payload['ip'] = $request->getClientIp();

        return $payload;
    }

    /**
     * @param array<string, mixed> $payload
     * @return array<string, mixed>
     */
    private function setUserInformations(array $payload): array
    {
        $user = $this->userRepository->findOneBy([
            'email' => $payload['username'],
        ]);

        if ($user instanceof User) {
            $payload['user_data'] = $user->getUserInformations();
        }

        return $payload;
    }
}
