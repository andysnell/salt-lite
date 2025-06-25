<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\DateTimeAware;

interface LocalTimeConstruct extends \Stringable, DateTimeAware
{
    public static function current(Clock $clock = new SystemClock()): self;
    public function next(): self;
    public function previous(): self;
}
