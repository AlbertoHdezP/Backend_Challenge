<?php

namespace Crimsoncircle\Model;

class LeapYear
{
    public function isLeapYear(int $year): int
    {
        //TODO: Logic must be implemented to calculate if a year is a leap year
        $leap = 0;

        if ($year) {
            $leap = date('L', mktime(0, 0, 0, 1, 1, $year));
        }
        return $leap;
    }
}