<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Enum;

use PhoneBurner\SaltLite\Tests\Fixtures\StoplightState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WithStringBackedInstanceStaticMethodTest extends TestCase
{
    #[Test]
    public function instanceReturnsExpectedInstance(): void
    {
        foreach (StoplightState::cases() as $case) {
            self::assertSame($case, StoplightState::instance($case));
            self::assertSame($case, StoplightState::instance($case->value));
            self::assertSame($case, StoplightState::instance(\strtoupper($case->value)));
            self::assertSame($case, StoplightState::instance(\strtolower($case->value)));
        }
    }

    #[Test]
    public function instanceThrowsInvalidArgumentExceptionOnBadValue(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        StoplightState::instance('invalid');
    }

    #[Test]
    public function instanceThrowsInvalidArgumentExceptionOnBadType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        StoplightState::instance(new \stdClass());
    }

    #[Test]
    public function castReturnsExpectedInstance(): void
    {
        foreach (StoplightState::cases() as $case) {
            self::assertSame($case, StoplightState::cast($case));
            self::assertSame($case, StoplightState::cast($case->value));
            self::assertSame($case, StoplightState::cast(\strtoupper($case->value)));
            self::assertSame($case, StoplightState::cast(\strtolower($case->value)));
        }
    }

    #[Test]
    public function castReturnsNullOnBadValue(): void
    {
        self::assertNull(StoplightState::cast('invalid'));
    }

    #[Test]
    public function castReturnsNullOnBadType(): void
    {
        self::assertNull(StoplightState::cast(new \stdClass()));
    }
}
