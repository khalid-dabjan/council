<?php

namespace App\Rules;

use App\Detection\Spam;

class SpamFree
{
    public static function passes($attribute, $value)
    {
        try {
            return !resolve(Spam::class)->detect($value);
        } catch (\Exception $exception) {
            return false;
        }

    }
}