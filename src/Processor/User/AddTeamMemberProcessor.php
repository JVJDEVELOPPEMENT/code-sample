<?php

declare(strict_types=1);

namespace App\Processor\User;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Entity\User;
use App\Enum\UserErrorEnum;
use App\InputPayload\User\AddTeamMemberInputPayload;
use App\OutputPayload\User\AddTeamMemberOutputPayload;
use App\Repository\CompanyRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

final class AddTeamMemberProcessor implements ProcessorInterface
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
        private UserRepository $userRepository,
        private TokenGeneratorInterface $tokenGenerator,
        private CompanyRepository $companyRepository
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
    ): AddTeamMemberOutputPayload {
        if ($data instanceof AddTeamMemberInputPayload === false) {
            throw new BadRequestHttpException();
        }

        $company = $this->companyRepository->find($data->companyId);

        if ($company instanceof Company === false) {
            throw new BadRequestHttpException('Company not found');
        }

        $dbUser = $this->userRepository->findOneBy([
            'email' => $data->email,
        ]);

        if ($dbUser instanceof User === true) {
            throw new BadRequestHttpException((UserErrorEnum::EMAIL_ALREADY_USED)->value);
        }

        $newUser = new User();
        $newUser->createTeamMember($data);

        $newUser->setCompany($company);

        $token = $this->tokenGenerator->generateToken();
        $password = mb_strcut($token, 5, 6);

        $newUser->setPassword($this->passwordHasher->hashPassword($newUser, $password));

        $this->userRepository->save($newUser, true);

        //TODO envoie mail au nouveau membre, -> welcome
        //TODO envoie mail au nouveau membre, -> verifie email

        return new AddTeamMemberOutputPayload(success: true, id: (int) $newUser->getId());
    }
}
