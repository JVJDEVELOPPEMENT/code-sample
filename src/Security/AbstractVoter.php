<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\Company;
use App\Entity\Shop;
use App\Entity\User;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

abstract class AbstractVoter extends Voter
{
    protected function supports(string $attribute, mixed $subject): bool
    {
        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        return true;
    }

    /**
     * @param null|Collection<int, Shop> $userShops
     */
    protected function isAllowedByCompanyShopCorporate(Shop $shop, ?Collection $userShops): bool
    {
        if ($userShops === null || count($userShops) === 0) {
            return false;
        } elseif (in_array($shop, $userShops->toArray(), true) === false) {
            return false;
        }
        return true;
    }

    /**
     * @return  null|Collection<int, Shop>
     */
    protected function getShopsByUser(User $user): ?Collection
    {
        $company = $user->getCompany();

        if ($company instanceof Company === false) {
            return null;
        }

        return $company->getShops();
    }
}
