<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Domain;

interface DayOfWeekAware
{
    public function getDayOfWeek(): DayOfWeek;
}
