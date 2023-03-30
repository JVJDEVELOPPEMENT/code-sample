<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\Shop;
use App\Entity\User;
use App\Security\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProductVoter extends AbstractVoter
{
    public const PRODUCT_EDIT = 'PRODUCT_EDIT';

    public const PRODUCT_CREATE = 'PRODUCT_CREATE';

    public const PRODUCT_DELETE = 'PRODUCT_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::PRODUCT_CREATE, self::PRODUCT_EDIT, self::PRODUCT_DELETE], true)
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (! $user instanceof User) {
            return false;
        }

        if (! $subject instanceof Product) {
            return false;
        }

        $productShop = $subject->getShop();

        if (! $productShop instanceof Shop) {
            return false;
        }

        return match ($attribute) {
            self::PRODUCT_CREATE, self::PRODUCT_EDIT, self::PRODUCT_DELETE => $this->isAllowedByCompanyShopCorporate(
                $productShop,
                $this->getShopsByUser($user)
            ),
            default => false,
        };
    }
}
