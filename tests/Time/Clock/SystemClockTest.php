<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\Clock;

use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\TimeUnit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    #[Test]
    public function nowHappyPath(): void
    {
        $before = new \DateTimeImmutable();
        $now = new SystemClock()->now();
        $after = new \DateTimeImmutable();

        self::assertGreaterThanOrEqual($before, $now);
        self::assertLessThanOrEqual($after, $now);
    }

    #[Test]
    public function timestampHappyPath(): void
    {
        $before = \time();
        $now = new SystemClock()->timestamp();
        $after = \time();

        self::assertGreaterThanOrEqual($before, $now);
        self::assertLessThanOrEqual($after, $now);
    }

    #[Test]
    public function microtimepHappyPath(): void
    {
        $before = \microtime(true);
        $now = new SystemClock()->microtime();
        $after = \microtime(true);

        self::assertGreaterThanOrEqual($before, $now);
        self::assertLessThanOrEqual($after, $now);
    }

    #[Test]
    #[DataProvider('providesSleepHappyPathTestsCases')]
    public function sleepHappyPath(int $delay, TimeUnit $unit, int $minimum): void
    {
        $before = (int)\hrtime(true);
        $return = new SystemClock()->sleep($delay, $unit);
        $duration = (int)\hrtime(true) - $before;

        self::assertTrue($return);
        self::assertGreaterThanOrEqual($minimum, $duration);
        self::assertLessThan(1.25 * $minimum, $duration);
    }

    public static function providesSleepHappyPathTestsCases(): \Generator
    {
        yield [1, TimeUnit::Second, TimeUnit::NANOSECONDS_IN_SECOND];
        yield [100, TimeUnit::Millisecond, 100 * TimeUnit::NANOSECONDS_IN_MILLISECOND];
        yield [25_000, TimeUnit::Microsecond, 25_000 * TimeUnit::NANOSECONDS_IN_MICROSECOND];
        yield [250_000_000, TimeUnit::Nanosecond, 250_000_000];
    }
}
