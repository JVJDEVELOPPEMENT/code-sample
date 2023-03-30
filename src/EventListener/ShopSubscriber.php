<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Company;
use App\Entity\Shop;
use App\Entity\User;
use App\Enum\EntityCode;
use App\Repository\AttachmentRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\String\Slugger\SluggerInterface;

final class ShopSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Security $security,
        private SluggerInterface $slugger,
        private AttachmentRepository $attachmentRepository
    ) {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => [
                ['setCreatedBy', EventPriorities::PRE_WRITE],
                ['setUpdatedBy', EventPriorities::PRE_WRITE],
                ['setSlug', EventPriorities::PRE_WRITE],
                ['setCoverImageAttachment', EventPriorities::PRE_WRITE],
                ['setLogoImageAttachment', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function setCreatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $shop = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $shop instanceof Shop || $method !== Request::METHOD_POST) {
            return;
        }

        $shop->setCreatedBy((int) $user->getId());
    }

    public function setUpdatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $shop = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $shop instanceof Shop || $method !== Request::METHOD_PUT) {
            return;
        }

        $shop->setUpdatedBy((int) $user->getId());
    }

    public function setSlug(ViewEvent $event): void
    {
        $shop = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $shop instanceof Shop || ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST)) {
            return;
        }

        /** @var Company $company */
        $company = $shop->getCompany();

        $slug = $this->slugger->slug($company->getName() . '-' . $shop->getName());

        $shop->setSlug((string) $slug);
    }

    public function setCoverImageAttachment(ViewEvent $event): void
    {
        $shop = $event->getControllerResult();

        $method = $event->getRequest()
            ->getMethod();

        if (! $shop instanceof Shop || ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST)) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $attachment_id = $propertyAccessor->getValue(
            json_decode($event->getRequest()->getContent(), true),
            '[cover_image_attachment_id]'
        );
        $attachment = $this->attachmentRepository->find($attachment_id);
        if ($attachment !== null) {
            $attachment->setRelatedTo(EntityCode::SHOP);
            $shop->setCoverImageAttachment($attachment);
        }
    }

    public function setLogoImageAttachment(ViewEvent $event): void
    {
        $shop = $event->getControllerResult();

        $method = $event->getRequest()
            ->getMethod();

        if (! $shop instanceof Shop || ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST)) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $attachment_id = $propertyAccessor->getValue(
            json_decode($event->getRequest()->getContent(), true),
            '[logo_image_attachment_id]'
        );
        $attachment = $this->attachmentRepository->find($attachment_id);
        if ($attachment !== null) {
            $attachment->setRelatedTo(EntityCode::SHOP);
            $shop->setLogoImageAttachment($attachment);
        }
    }
}
