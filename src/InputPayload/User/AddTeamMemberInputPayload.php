<?php

declare(strict_types=1);

namespace App\InputPayload\User;

final readonly class AddTeamMemberInputPayload
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $phone,
        public string $email,
        public int $companyId
    ) {
    }
}
