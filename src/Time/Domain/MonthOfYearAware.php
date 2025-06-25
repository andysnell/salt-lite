<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Domain;

interface MonthOfYearAware
{
    public function getMonth(): MonthOfYear;
}
