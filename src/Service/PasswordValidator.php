<?php


namespace App\Service;


class PasswordValidator
{
    const MIN_LENGTH = 8;

    public function isValid(string $password): bool
    {
        if ($password >= self::MIN_LENGTH && $password !== null) {
            return true;
        }
        return false;
    }
}