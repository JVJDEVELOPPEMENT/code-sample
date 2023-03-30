<?php

declare(strict_types=1);

namespace App\Processor\Company;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Company;
use App\Enum\EntityCode;
use App\InputPayload\Company\UpdateCompanyVisuelInputPayload;
use App\Repository\AttachmentRepository;
use App\Repository\CompanyRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class CompanyVisuelProcessor implements ProcessorInterface
{
    public function __construct(
        private CompanyRepository $companyRepository,
        private AttachmentRepository $attachmentRepository,
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
    ): Company {
        if ($data instanceof UpdateCompanyVisuelInputPayload === false) {
            throw new BadRequestHttpException();
        }
        /** @var UpdateCompanyVisuelInputPayload $data */

        $company = $this->companyRepository->find($uriVariables['id']);
        $attachment = $this->attachmentRepository->find($data->attachmentId);
        if ($company === null || $attachment === null) {
            throw new BadRequestHttpException('Company or Attachment not found');
        }
        $attachment->setRelatedTo(EntityCode::COMPANY);
        $company->setAttachment($attachment);
        $this->companyRepository->save($company, true);

        return $company;
    }
}
