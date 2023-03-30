<?php

declare(strict_types=1);

namespace App\EventListener;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Company;
use App\Entity\Product;
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

final class ProductSubscriber implements EventSubscriberInterface
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
                ['setAttachment', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function setCreatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $product = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $product instanceof Product || $method !== Request::METHOD_POST) {
            return;
        }

        $product->setCreatedBy((int) $user->getId());
    }

    public function setUpdatedBy(ViewEvent $event): void
    {
        $user = $this->security->getUser();

        if ($user instanceof User === false) {
            return;
        }

        $product = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $product instanceof Product || $method !== Request::METHOD_PUT) {
            return;
        }

        $product->setUpdatedBy((int) $user->getId());
    }

    public function setSlug(ViewEvent $event): void
    {
        $product = $event->getControllerResult();
        $method = $event->getRequest()
            ->getMethod();

        if (! $product instanceof Product || ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST)) {
            return;
        }

        /** @var Shop $shop */
        $shop = $product->getShop();

        /** @var Company $company */
        $company = $shop->getCompany();

        $slug = $this->slugger->slug($company->getName() . '-' . $shop->getName() . '-' . $product->getTitle());

        $product->setSlug((string) $slug);
    }

    public function setAttachment(ViewEvent $event): void
    {
        $product = $event->getControllerResult();

        $method = $event->getRequest()
            ->getMethod();

        if (! $product instanceof Product || ($method !== Request::METHOD_PUT && $method !== Request::METHOD_POST)) {
            return;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $attachment_id = $propertyAccessor->getValue(
            json_decode($event->getRequest()->getContent(), true),
            '[attachment_id]'
        );
        $attachment = $this->attachmentRepository->find($attachment_id);
        if ($attachment !== null) {
            $attachment->setRelatedTo(EntityCode::PRODUCT);
            $product->setAttachment($attachment);
        }
    }
}
