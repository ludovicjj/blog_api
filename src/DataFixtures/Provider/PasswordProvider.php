<?php

namespace App\DataFixtures\Provider;

class PasswordProvider
{
    public static function hashPassword($str)
    {
        return $str;
    }
}