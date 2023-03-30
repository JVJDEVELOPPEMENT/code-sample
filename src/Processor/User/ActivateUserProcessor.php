<?php

declare(strict_types=1);

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\OutputPayload\User\ActivateUserResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class ActivateUserProcessor implements ProcessorInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    /**
     * @param array<array-key, mixed> $uriVariables
     * @param array<array-key, mixed> $context
     */
    public function process(
        mixed $data,
        Operation $operation,
        array $uriVariables = [],
        array $context = []
    ): ActivateUserResponse {
        $user = $context['previous_data'];

        if ($user instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user who initiate action');
        }

        $userToActivate = $this->userRepository->find($user->getId());

        if ($userToActivate instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user to activate');
        }

        $userToActivate->setIsActive(true);

        $this->userRepository->save($userToActivate, true);

        return new ActivateUserResponse(success: true);
    }
}
