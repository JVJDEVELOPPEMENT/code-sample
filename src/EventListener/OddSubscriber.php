<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Odd;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class OddSubscriber implements EventSubscriberInterface
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

        $odd = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $odd instanceof Odd || $method !== Request::METHOD_POST) {
            return;
        }

        $odd->setCreatedBy((int) $user->getId());
    }

    public function setUpdatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $odd = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $odd instanceof Odd || $method !== Request::METHOD_PUT) {
            return;
        }

        $odd->setUpdatedBy((int) $user->getId());
    }
}
