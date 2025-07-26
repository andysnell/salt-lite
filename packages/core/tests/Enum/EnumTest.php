<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Enum;

use PhoneBurner\SaltLite\Enum\Enum;
use PhoneBurner\SaltLite\Tests\Fixtures\IntBackedEnum;
use PhoneBurner\SaltLite\Tests\Fixtures\StoplightState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Enum::class)]
final class EnumTest extends TestCase
{
    #[Test]
    public function valuesWithSingleIntEnum(): void
    {
        $result = Enum::values(IntBackedEnum::Bar);
        self::assertSame([2], $result);
    }

    #[Test]
    public function valuesWithMultipleIntEnums(): void
    {
        $result = Enum::values(IntBackedEnum::Foo, IntBackedEnum::Baz);
        self::assertSame([1, 3], $result);
    }

    #[Test]
    public function valuesWithSingleStringEnum(): void
    {
        $result = Enum::values(StoplightState::Red);
        self::assertSame(['red'], $result);
    }

    #[Test]
    public function valuesWithMultipleStringEnums(): void
    {
        $result = Enum::values(StoplightState::Yellow, StoplightState::Green);
        self::assertSame(['yellow', 'green'], $result);
    }

    #[Test]
    public function valuesWithMixedEnums(): void
    {
        $result = Enum::values(IntBackedEnum::Foo, StoplightState::Red, IntBackedEnum::Baz);
        self::assertSame([1, 'red', 3], $result);
    }

    #[Test]
    public function valuesWithNoEnums(): void
    {
        $result = Enum::values();
        self::assertSame([], $result);
    }
}
