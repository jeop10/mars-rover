<?php

namespace App\Nasa\Support;

class Compass
{
    const NORTH = 'N';
    const SOUTH = 'S';
    const EAST = 'E';
    const WEST = 'W';
    const MINGRID = 0;
    const MAXGRID = 199;

    public static function isPointOutOfBounds($x, $y): bool
    {
        if ($x < self::MINGRID || $x > self::MAXGRID) {
            return true;
        }

        if ($y < self::MINGRID || $y > self::MAXGRID) {
            return true;
        }

        return false;
    }
}
