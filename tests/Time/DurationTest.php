<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time;

use PhoneBurner\SaltLite\Time\Duration;
use PhoneBurner\SaltLite\Time\TimeUnit;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DurationTest extends TestCase
{
    #[Test]
    #[DataProvider('iso8601DurationProvider')]
    public function parseHappyPath(string $duration, array $expected, string|null $formatted = null): void
    {
        // test constructor
        $sut = new Duration(...$expected);
        foreach (Duration::UNITS as $unit) {
            self::assertSame($expected[$unit], $sut->$unit);
        }
        self::assertSame($formatted ?? $duration, (string)$sut);

        // test upper case duration string
        $sut = Duration::parse($duration);
        foreach (Duration::UNITS as $unit) {
            self::assertSame($expected[$unit], $sut?->$unit);
        }

        // test lower case duration string
        $sut = Duration::parse(\strtolower($duration));
        foreach (Duration::UNITS as $unit) {
            self::assertSame($expected[$unit], $sut?->$unit);
        }

        // DateInterval cannot be constructed with duration strings with fractional seconds
        if ($expected['microseconds'] !== 0) {
            return;
        }

        $sut = Duration::make(new \DateInterval($duration));

        // DateInterval converts weeks to days;
        if ($expected['weeks'] !== 0) {
            $expected['days'] = $expected['weeks'] * TimeUnit::DAYS_IN_WEEK;
            $expected['weeks'] = 0;
        }

        foreach (Duration::UNITS as $unit) {
            self::assertSame($expected[$unit], $sut->$unit);
        }
    }

    public static function iso8601DurationProvider(): \Generator
    {
        yield ['P3W', [
            'weeks' => 3,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P1W', [
            'weeks' => 1,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P52W', [
            'weeks' => 52,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P104W', [
            'weeks' => 104,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P0W', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ], Duration::EMPTY_DURATION];

        yield ['P1Y', [
            'weeks' => 0,
            'years' => 1,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P2M', [
            'weeks' => 0,
            'years' => 0,
            'months' => 2,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P10D', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 10,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P1Y2M', [
            'weeks' => 0,
            'years' => 1,
            'months' => 2,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P3Y5D', [
            'weeks' => 0,
            'years' => 3,
            'months' => 0,
            'days' => 5,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['PT5H', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 5,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['PT30M', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 30,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['PT45S', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 45,
            'microseconds' => 0,
        ]];

        yield ['PT1H15M', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 1,
            'minutes' => 15,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['PT20.5S', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 20,
            'microseconds' => 500000,
        ]];

        yield ['P1Y2M3DT4H5M6S', [
            'weeks' => 0,
            'years' => 1,
            'months' => 2,
            'days' => 3,
            'hours' => 4,
            'minutes' => 5,
            'seconds' => 6,
            'microseconds' => 0,
        ]];

        yield ['P2DT3H', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 2,
            'hours' => 3,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ]];

        yield ['P3Y6M4DT12H30M5.75S', [
            'weeks' => 0,
            'years' => 3,
            'months' => 6,
            'days' => 4,
            'hours' => 12,
            'minutes' => 30,
            'seconds' => 5,
            'microseconds' => 750000,
        ]];

        yield ['P00Y00M00DT00H00M00S', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ], Duration::EMPTY_DURATION];

        yield ['P5DT0H0M0S', [
            'weeks' => 0,
            'years' => 0,
            'months' => 0,
            'days' => 5,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'microseconds' => 0,
        ], 'P5D'];

        yield ['P7Y11M29DT23H59M59.999999S', [
            'weeks' => 0,
            'years' => 7,
            'months' => 11,
            'days' => 29,
            'hours' => 23,
            'minutes' => 59,
            'seconds' => 59,
            'microseconds' => 999999,
        ]];
    }
}
