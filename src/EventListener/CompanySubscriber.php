<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Company;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class CompanySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['setCreatedBy', EventPriorities::PRE_WRITE],
                ['setUpdatedBy', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function setCreatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $company = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $company instanceof Company || $method !== Request::METHOD_POST) {
            return;
        }

        $company->setCreatedBy((int) $user->getId());

        if ($user->getCompany() === null) {
            $user->setCompany($company);
        }
    }

    public function setUpdatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $company = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $company instanceof Company || $method !== Request::METHOD_PUT) {
            return;
        }

        $company->setUpdatedBy((int) $user->getId());
    }
}
