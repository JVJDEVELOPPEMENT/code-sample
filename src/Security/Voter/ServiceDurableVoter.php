<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\ServiceDurable;
use App\Entity\Shop;
use App\Entity\User;
use App\Security\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ServiceDurableVoter extends AbstractVoter
{
    public const SERVICE_DURABLE_EDIT = 'SERVICE_DURABLE_EDIT';

    public const SERVICE_DURABLE_CREATE = 'SERVICE_DURABLE_CREATE';

    public const SERVICE_DURABLE_DELETE = 'SERVICE_DURABLE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array(
            $attribute,
            [self::SERVICE_DURABLE_CREATE, self::SERVICE_DURABLE_EDIT, self::SERVICE_DURABLE_DELETE],
            true
        )
            && $subject instanceof ServiceDurable;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        if (! $subject instanceof ServiceDurable) {
            return false;
        }

        $serviceDurableShop = $subject->getShop();

        if (! $serviceDurableShop instanceof Shop) {
            return false;
        }

        return match ($attribute) {
            self::SERVICE_DURABLE_CREATE, self::SERVICE_DURABLE_EDIT, self::SERVICE_DURABLE_DELETE => $this->isAllowedByCompanyShopCorporate(
                $serviceDurableShop,
                $this->getShopsByUser($user)
            ),
            default => false,
        };
    }
}
