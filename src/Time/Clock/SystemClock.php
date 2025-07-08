<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Time\TimeUnit;

class SystemClock implements Clock
{
    #[\Override]
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }

    /**
     * @return positive-int
     */
    #[\Override]
    public function timestamp(): int
    {
        return \time();
    }

    #[\Override]
    public function microtime(): float
    {
        return \microtime(true);
    }

    #[\Override]
    public function sleep(int $delay, TimeUnit $unit = TimeUnit::Microsecond): bool
    {
        $delay >= 0 || throw new \UnexpectedValueException('Delay must be greater than or equal to zero.');
        $nanoseconds = match ($unit) {
            TimeUnit::Second => $delay * TimeUnit::NANOSECONDS_IN_SECOND,
            TimeUnit::Millisecond => $delay * TimeUnit::NANOSECONDS_IN_MILLISECOND,
            TimeUnit::Microsecond => $delay * TimeUnit::NANOSECONDS_IN_MICROSECOND,
            TimeUnit::Nanosecond => $delay,
            default => throw new \UnexpectedValueException('Unsupported time unit for sleep(): ' . $unit->name),
        };
        $seconds = \intdiv($nanoseconds, TimeUnit::NANOSECONDS_IN_SECOND);
        $nanoseconds %= TimeUnit::NANOSECONDS_IN_SECOND;

        return \time_nanosleep($seconds, $nanoseconds) === true;
    }
}
