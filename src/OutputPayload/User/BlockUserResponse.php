<?php

declare(strict_types=1);

namespace App\OutputPayload\User;

final readonly class BlockUserResponse
{
    public function __construct(
        public bool $success,
        public string $message = ''
    ) {
    }
}
