<?php

namespace App\Seagon;

class Random
{
    public static function threshold(int $percentage): bool
    {
        $rand = rand(0, 100);

        return $rand < $percentage;
    }
}
