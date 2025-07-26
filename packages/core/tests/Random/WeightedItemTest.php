<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Random;

use PhoneBurner\SaltLite\Random\WeightedItem;
use PhoneBurner\SaltLite\Tests\Fixtures\StoplightState;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class WeightedItemTest extends TestCase
{
    #[Test]
    #[TestWith(['apple', 1])]
    #[TestWith([StoplightState::Red, 0])]
    #[TestWith([StoplightState::Green, 100])]
    #[TestWith([StoplightState::Yellow, \PHP_INT_MAX])]
    public function happyPath(mixed $value, int $weight): void
    {
        \assert($weight >= 0);
        $item = new WeightedItem($value, $weight);
        self::assertSame($value, $item->value);
        self::assertSame($weight, $item->weight);
    }

    #[Test]
    public function weightMustBeNonNegative(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('weight must be a positive integer.');
        /** @phpstan-ignore argument.type (intentionally negative) */
        new WeightedItem('apple', -1);
    }
}
