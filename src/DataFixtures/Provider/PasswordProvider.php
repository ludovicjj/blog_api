<?php

namespace App\DataFixtures\Provider;

class PasswordProvider
{
    public static function hashPassword($password): string
    {
        return $password;
    }
}