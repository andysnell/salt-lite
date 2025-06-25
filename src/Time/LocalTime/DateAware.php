<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

interface DateAware
{
    public function getDate(): Date;
}
