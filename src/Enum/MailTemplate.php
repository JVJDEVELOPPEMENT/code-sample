<?php

declare(strict_types=1);

namespace App\Enum;

enum MailTemplate: string
{
    case WELCOME = 'WELCOME';
        //    case ACCOUNT_VALIDATION = 'ACCOUNT_VALIDATION';
    case ACCOUNT_VALIDATION = 'Activez votre compte codesample';
    case ACCOUNT_CHECKED = 'ACCOUNT_CHECKED';
}
