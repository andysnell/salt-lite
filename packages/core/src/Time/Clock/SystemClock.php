<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Clock;

use Carbon\CarbonImmutable;

class SystemClock implements Clock
{
    #[\Override]
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }
}
