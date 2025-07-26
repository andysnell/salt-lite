<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Time\Clock\StaticClock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StaticClockTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $now = CarbonImmutable::now();

        $clock = new StaticClock($now);

        self::assertSame($now, $clock->now());
        \sleep(1);
        self::assertSame($now, $clock->now());
    }
}
