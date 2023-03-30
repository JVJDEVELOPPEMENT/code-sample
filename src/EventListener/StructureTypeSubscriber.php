<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\StructureType;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class StructureTypeSubscriber implements EventSubscriberInterface
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

        $structureType = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $structureType instanceof StructureType || $method !== Request::METHOD_POST) {
            return;
        }

        $structureType->setCreatedBy((int) $user->getId());
    }

    public function setUpdatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $structureType = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $structureType instanceof StructureType || $method !== Request::METHOD_PUT) {
            return;
        }

        $structureType->setUpdatedBy((int) $user->getId());
    }
}
