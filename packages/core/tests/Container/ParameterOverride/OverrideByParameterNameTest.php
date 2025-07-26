<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ParameterOverride;

use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterName;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class OverrideByParameterNameTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $override = new OverrideByParameterName('foo', 'bar');
        self::assertSame('foo', $override->identifier());
        self::assertSame('bar', $override->value());
        self::assertSame(OverrideType::Name, $override->type());

        $override = new OverrideByParameterName('other');
        self::assertSame('other', $override->identifier());
        self::assertNull($override->value());
        self::assertSame(OverrideType::Name, $override->type());
    }

    #[Test]
    public function identifierMustBeNonempty(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        new OverrideByParameterName('', 'bar');
    }
}
