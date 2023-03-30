<?php

declare(strict_types=1);

namespace App\Enum;

enum UserErrorEnum: string
{
    case INVALID_PASSWORD = 'INVALID_PASSWORD';
    case EMAIL_ALREADY_USED = 'EMAIL_ALREADY_USED';
}
