<?php

declare(strict_types=1);

namespace App\InputPayload\Company;

final readonly class UpdateCompanyVisuelInputPayload
{
    public function __construct(
        public string $attachmentId,
    ) {
    }
}
