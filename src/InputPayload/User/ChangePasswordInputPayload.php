<?php

declare(strict_types=1);

namespace App\InputPayload\User;

final readonly class ChangePasswordInputPayload
{
    public function __construct(
        public string $oldPassword,
        public string $newPassword,
    ) {
    }
}
