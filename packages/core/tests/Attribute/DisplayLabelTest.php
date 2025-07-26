<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use PhoneBurner\SaltLite\Attribute\DisplayLabel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DisplayLabelTest extends TestCase
{
    #[Test]
    public function constructorSetsValueProperty(): void
    {
        $label = new DisplayLabel('Test Label');
        self::assertSame('Test Label', $label->value);
    }

    #[Test]
    public function toStringReturnsValue(): void
    {
        $label = new DisplayLabel('Test Label');
        self::assertSame('Test Label', (string)$label);
    }
}
