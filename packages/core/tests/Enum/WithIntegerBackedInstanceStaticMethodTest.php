<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Enum;

use PhoneBurner\SaltLite\Tests\Fixtures\ArabicNumerals;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WithIntegerBackedInstanceStaticMethodTest extends TestCase
{
    #[Test]
    public function instanceReturnsExpectedInstance(): void
    {
        foreach (ArabicNumerals::cases() as $case) {
            self::assertSame($case, ArabicNumerals::instance($case));
            self::assertSame($case, ArabicNumerals::instance($case->value));
            self::assertSame($case, ArabicNumerals::instance((string)$case->value));
        }
    }

    #[Test]
    public function instanceThrowsInvalidArgumentException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        ArabicNumerals::instance('invalid');
    }

    #[Test]
    public function castReturnsExpectedInstance(): void
    {
        foreach (ArabicNumerals::cases() as $case) {
            self::assertSame($case, ArabicNumerals::cast($case));
            self::assertSame($case, ArabicNumerals::cast($case->value));
            self::assertSame($case, ArabicNumerals::cast((string)$case->value));
        }
    }

    #[Test]
    public function castReturnsNullForInvalidValue(): void
    {
        self::assertNull(ArabicNumerals::cast('invalid'));
    }
}
