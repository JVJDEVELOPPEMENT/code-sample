<?php

declare(strict_types=1);

namespace App\OutputPayload\User;

final readonly class AddTeamMemberOutputPayload
{
    public function __construct(
        public bool $success,
        public int $id,
        public string $message = '',
    ) {
    }
}
