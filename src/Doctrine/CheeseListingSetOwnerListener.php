<?php

namespace App\Doctrine;

use App\Entity\CheeseListing;
use App\Entity\User;
use Symfony\Component\Security\Core\Security;

class CheeseListingSetOwnerListener
{
    public function __construct(private Security $security)
    {
    }

    public function prePersist(CheeseListing $cheeseListing)
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if ($cheeseListing->getOwner()) {
            return;
        }

        if ($user) {
            $cheeseListing->setOwner($user);
        }
    }
}