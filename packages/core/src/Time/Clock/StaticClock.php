<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Clock;

use Carbon\CarbonImmutable;

final readonly class StaticClock implements Clock
{
    private CarbonImmutable $now;

    public function __construct(\DateTimeInterface|string|null $now = new CarbonImmutable())
    {
        $this->now = match (true) {
            $now instanceof CarbonImmutable => $now,
            $now instanceof \DateTimeInterface => CarbonImmutable::instance($now),
            default => new CarbonImmutable($now),
        };
    }

    #[\Override]
    public function now(): CarbonImmutable
    {
        return $this->now;
    }
}
