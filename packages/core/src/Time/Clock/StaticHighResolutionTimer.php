<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Clock;

class StaticHighResolutionTimer implements HighResolutionTimer
{
    public function __construct(private readonly int $now)
    {
    }

    #[\Override]
    public function now(): int
    {
        return $this->now;
    }
}
