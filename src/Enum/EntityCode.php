<?php

declare(strict_types=1);

namespace App\Enum;

enum EntityCode: string
{
    case COMPANY = 'COMPANY';
    case SHOP = 'SHOP';
    case PRODUCT = 'PRODUCT';
    case SERVICE_DURABLE = 'SERVICE_DURABLE';
}
