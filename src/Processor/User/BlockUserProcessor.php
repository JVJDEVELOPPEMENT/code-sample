<?php

declare(strict_types=1);

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\OutputPayload\User\BlockUserResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class BlockUserProcessor implements ProcessorInterface
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
    ): BlockUserResponse {
        $user = $context['previous_data'];

        if ($user instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user who initiate action');
        }

        $userToBlock = $this->userRepository->find($user->getId());

        if ($userToBlock instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user to block');
        }

        $userToBlock->setIsActive(false);

        $this->userRepository->save($userToBlock, true);

        return new BlockUserResponse(success: true);
    }
}
