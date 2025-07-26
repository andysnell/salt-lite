<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\TimeZone;

use PhoneBurner\SaltLite\Iterator\Arr;
use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneCollection;
use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneFactory;
use PhoneBurner\SaltLite\Time\TimeZone\Tz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tz::class)]
#[CoversClass(TimeZoneFactory::class)]
final class TimeZoneFactoryTest extends TestCase
{
    /**
     * @param value-of<Tz>&string $time_zone_name
     */
    #[DataProvider('providesTimeZoneNames')]
    #[Test]
    public function makeReturnsMemoizedTimeZone(string $time_zone_name): void
    {
        $tz = TimeZoneFactory::make($time_zone_name);
        self::assertSame($tz, TimeZoneFactory::make($time_zone_name));
        self::assertSame($time_zone_name, $tz->getName());
    }

    public static function providesTimeZoneNames(): \Generator
    {
        yield from \array_map(Arr::wrap(...), \array_column(Tz::cases(), 'value'));
    }

    #[Test]
    public function collectReturnsEmptyTimeZoneCollection(): void
    {
        $collection = TimeZoneFactory::collect();
        self::assertEmpty($collection);
        self::assertSame($collection, TimeZoneFactory::collect());
        self::assertEquals($collection, TimeZoneCollection::make());
    }

    #[Test]
    public function collectReturnsMemoizedTimeZoneCollection(): void
    {
        $collection = TimeZoneFactory::collect(
            Tz::NewYork,
            Tz::Chicago,
            Tz::Denver,
            Tz::LosAngeles,
        );

        self::assertCount(4, $collection);

        self::assertSame($collection, TimeZoneFactory::collect(
            Tz::NewYork,
            Tz::Chicago,
            Tz::Denver,
            Tz::LosAngeles,
        ));

        self::assertSame($collection, TimeZoneFactory::collect(
            new \DateTimeZone(Tz::NewYork->value),
            new \DateTimeZone(Tz::Chicago->value),
            new \DateTimeZone(Tz::Denver->value),
            new \DateTimeZone(Tz::LosAngeles->value),
        ));
    }
}
