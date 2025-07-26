<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ParameterOverride;

use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterPosition;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OverrideByParameterPositionTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $override = new OverrideByParameterPosition(2, 'bar');
        self::assertSame(2, $override->identifier());
        self::assertSame('bar', $override->value());
        self::assertSame(OverrideType::Position, $override->type());

        $override = new OverrideByParameterPosition(0);
        self::assertSame(0, $override->identifier());
        self::assertNull($override->value());
        self::assertSame(OverrideType::Position, $override->type());
    }

    #[Test]
    public function identifierMustBeNonempty(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        new OverrideByParameterPosition(-1, 'bar');
    }
}
