<?php


namespace App\Service;


class AuctionValidator
{

    public function isValid(int $price, string $name)
    {
        if ($price > 0 && strlen($name) >= 8) {
            return true;
        }
        return false;
    }
}