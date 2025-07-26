<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Enum;

use PhoneBurner\SaltLite\Tests\Fixtures\UnitStoplightState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WithUnitEnumInstanceStaticMethodTest extends TestCase
{
    #[Test]
    public function instanceReturnsExpectedInstance(): void
    {
        foreach (UnitStoplightState::cases() as $case) {
            self::assertSame($case, UnitStoplightState::instance($case));
            self::assertSame($case, UnitStoplightState::instance($case->name));
            self::assertSame($case, UnitStoplightState::instance(\strtoupper($case->name)));
            self::assertSame($case, UnitStoplightState::instance(\strtolower($case->name)));
        }
    }

    #[Test]
    public function instanceThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        UnitStoplightState::instance('invalid');
    }

    #[Test]
    public function castReturnsExpectedInstance(): void
    {
        foreach (UnitStoplightState::cases() as $case) {
            self::assertSame($case, UnitStoplightState::cast($case));
            self::assertSame($case, UnitStoplightState::cast($case->name));
            self::assertSame($case, UnitStoplightState::cast(\strtoupper($case->name)));
            self::assertSame($case, UnitStoplightState::cast(\strtolower($case->name)));
        }
    }

    #[Test]
    public function castReturnsNullForInvalidValue(): void
    {
        self::assertNull(UnitStoplightState::cast('invalid'));
    }
}
