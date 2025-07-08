<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Time\TimeUnit;
use Psr\Clock\ClockInterface;

interface Clock extends ClockInterface
{
    /**
     * Returns the current time as a DateTimeImmutable object, as required by the
     * proposed PSR, more specifically, an instance of CarbonImmutable
     */
    public function now(): CarbonImmutable;

    /**
     * Returns the Unix timestamp as an integer number of seconds
     */
    public function timestamp(): int;

    /**
     * Returns the unix timestamp with fractional seconds to the nearest
     * microsecond.
     */
    public function microtime(): float;

    /**
     * Do nothing for the given number of time units.
     */
    public function sleep(int $delay, TimeUnit $unit = TimeUnit::Microsecond): bool;
}
