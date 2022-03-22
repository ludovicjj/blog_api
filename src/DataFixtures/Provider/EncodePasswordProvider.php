<?php

namespace App\DataFixtures\Provider;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class EncodePasswordProvider
{
    public function __construct(private UserPasswordHasherInterface $hasher)
    {
    }

    public function hashPassword($plainPassword): string
    {
        return $this->hasher->hashPassword(new User(), $plainPassword);
    }
}