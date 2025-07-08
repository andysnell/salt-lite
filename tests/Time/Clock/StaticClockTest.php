<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Time\Clock\StaticClock;
use PhoneBurner\SaltLite\Time\TimeUnit;
use PHPUnit\Framework\Attributes\DataProvider;
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

    #[Test]
    public function timestampHappyPath(): void
    {
        $now = CarbonImmutable::now();
        $timestamp = $now->getTimestamp();

        $clock = new StaticClock($now);

        self::assertSame($timestamp, $clock->timestamp());
        \sleep(1);
        self::assertSame($timestamp, $clock->timestamp());
    }

    #[Test]
    public function microtimeHappyPath(): void
    {
        $now = CarbonImmutable::now();
        $timestamp = (float)$now->format('U.u');

        $clock = new StaticClock($now);

        self::assertSame($timestamp, $clock->microtime());
        \sleep(1);
        self::assertSame($timestamp, $clock->microtime());
    }

    #[Test]
    #[DataProvider('providesSleepHappyPathTestsCases')]
    public function sleepHappyPath(int $delay, TimeUnit $unit, int $maximum): void
    {
        $now = CarbonImmutable::now();

        $before = (int)\hrtime(true);
        $return = new StaticClock($now)->sleep($delay, $unit);
        $duration = (int)\hrtime(true) - $before;

        self::assertTrue($return);
        self::assertGreaterThanOrEqual(0, $duration);
        self::assertLessThanOrEqual($maximum, $duration);
    }

    public static function providesSleepHappyPathTestsCases(): \Generator
    {
        $maximum = 100 * TimeUnit::NANOSECONDS_IN_MICROSECOND;
        yield [60, TimeUnit::Second, $maximum];
        yield [1, TimeUnit::Second, $maximum];
        yield [100, TimeUnit::Millisecond, $maximum];
        yield [25_000, TimeUnit::Microsecond, $maximum];
        yield [250_000_000, TimeUnit::Nanosecond, $maximum];
    }
}
