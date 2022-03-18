<?php

namespace App\Security\Voter;

use App\Entity\CheeseListing;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

class CheeseListingVoter extends Voter
{
    const EDIT = "CHEESE_EDIT";

    public function __construct(private Security $security)
    {

    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT]) && $subject instanceof CheeseListing;
    }

    /**
     * @param string $attribute
     * @param CheeseListing $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
        }

        throw new \LogicException(sprintf('Did you forget to handle attribute "%s"', $attribute));
    }

    private function canEdit(CheeseListing $subject, User $user): bool
    {
        if ($subject->getOwner() === $user) {
            return true;
        }

        if ($this->security->isGranted("ROLE_ADMIN")) {
            return true;
        }

        return false;
    }
}