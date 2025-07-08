<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time;

use PhoneBurner\SaltLite\Time\TimeUnit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TimeUnitTest extends TestCase
{
    #[Test]
    #[DataProvider('providesFixedDurationTestCases')]
    public function hasFixedDurationReturnsExpectedValue(TimeUnit $unit, bool $expected): void
    {
        self::assertSame($expected, $unit->isFixedLengthUnit());
    }

    public static function providesFixedDurationTestCases(): \Generator
    {
        yield [TimeUnit::Year, false];
        yield [TimeUnit::Month, false];
        yield [TimeUnit::Week, true];
        yield [TimeUnit::Day, true];
        yield [TimeUnit::Hour, true];
        yield [TimeUnit::Minute, true];
        yield [TimeUnit::Second, true];
        yield [TimeUnit::Millisecond, true];
        yield [TimeUnit::Microsecond, true];
    }
}
