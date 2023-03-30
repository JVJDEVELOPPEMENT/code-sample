<?php

declare(strict_types=1);

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\User;
use App\Enum\UserErrorEnum;
use App\InputPayload\User\ChangePasswordInputPayload;
use App\OutputPayload\User\ChangePasswordResponse;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserPasswordUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $repository
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
    ): ChangePasswordResponse {
        if ($data instanceof ChangePasswordInputPayload === false) {
            throw new BadRequestHttpException();
        }
        /** @var ChangePasswordInputPayload $data */
        $user = $context['previous_data'];

        if ($user instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user');
        }

        $dbUser = $this->repository->find($user->getId());
        if ($dbUser instanceof User === false) {
            throw new BadRequestHttpException('Unable to identify user');
        }
        if ($this->passwordHasher->isPasswordValid($user, $data->oldPassword) === true) {
            $this->repository->upgradePassword(
                $dbUser,
                $this->passwordHasher->hashPassword($dbUser, $data->newPassword)
            );
            return new ChangePasswordResponse(success: true);
        }
        throw new BadRequestHttpException((UserErrorEnum::INVALID_PASSWORD)->value);
    }
}
