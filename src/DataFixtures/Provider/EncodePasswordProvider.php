<?php

namespace App\DataFixtures\Provider;

class EncoderPasswordProvider
{
    public static function hashPassword($password): string
    {
        return $password;
    }
}