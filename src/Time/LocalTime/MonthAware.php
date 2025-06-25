<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

interface MonthAware
{
    public function getMonth(): Month;
}
