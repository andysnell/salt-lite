<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

interface YearAware
{
    public function getYear(): Year;
}
