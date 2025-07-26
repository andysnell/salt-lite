<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Domain\Memory\Unit;

use PhoneBurner\SaltLite\Domain\Memory\Unit\DecimalMemoryUnit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(DecimalMemoryUnit::class)]
final class DecimalMemoryUnitTest extends TestCase
{
    #[Test]
    #[DataProvider('provideCasesWithValueAndSymbol')]
    public function caseHasCorrectValueAndSymbol(
        DecimalMemoryUnit $unit,
        int $expected_value,
        string $expected_symbol,
    ): void {
        self::assertSame($expected_value, $unit->value);
        self::assertSame($expected_symbol, $unit->symbol());
    }

    public static function provideCasesWithValueAndSymbol(): \Generator
    {
        yield 'Byte' => [DecimalMemoryUnit::Byte, 1, 'B'];
        yield 'Kilobyte' => [DecimalMemoryUnit::Kilobyte, 1000, 'KB'];
        yield 'Megabyte' => [DecimalMemoryUnit::Megabyte, 1_000_000, 'MB'];
        yield 'Gigabyte' => [DecimalMemoryUnit::Gigabyte, 1_000_000_000, 'GB'];
        yield 'Terabyte' => [DecimalMemoryUnit::Terabyte, 1_000_000_000_000, 'TB'];
        yield 'Petabyte' => [DecimalMemoryUnit::Petabyte, 1_000_000_000_000_000, 'PB'];
        yield 'Exabyte' => [DecimalMemoryUnit::Exabyte, 1_000_000_000_000_000_000, 'EB'];
    }

    #[Test]
    #[DataProvider('provideFitValues')]
    public function fitReturnsCorrectUnit(int $value, DecimalMemoryUnit $expected_unit): void
    {
        self::assertSame($expected_unit, DecimalMemoryUnit::fit($value));
    }

    public static function provideFitValues(): \Generator
    {
        $base = DecimalMemoryUnit::BASE;
        yield 'Zero' => [0, DecimalMemoryUnit::Byte];
        yield 'One Byte' => [1, DecimalMemoryUnit::Byte];
        yield '999 Bytes' => [$base - 1, DecimalMemoryUnit::Byte];
        yield 'Exactly 1 KB' => [$base, DecimalMemoryUnit::Kilobyte];
        yield '1.5 KB' => [(int)(1.5 * $base), DecimalMemoryUnit::Kilobyte];
        yield 'Exactly 1 MB' => [$base ** 2, DecimalMemoryUnit::Megabyte];
        yield '2.7 MB' => [(int)(2.7 * $base ** 2), DecimalMemoryUnit::Megabyte];
        yield 'Exactly 1 GB' => [$base ** 3, DecimalMemoryUnit::Gigabyte];
        yield 'Exactly 1 TB' => [$base ** 4, DecimalMemoryUnit::Terabyte];
        yield 'Exactly 1 PB' => [$base ** 5, DecimalMemoryUnit::Petabyte];
        yield 'Exactly 1 EB' => [$base ** 6, DecimalMemoryUnit::Exabyte];
        yield 'Negative One Byte' => [-1, DecimalMemoryUnit::Byte];
        yield 'Negative 1 KB' => [-$base, DecimalMemoryUnit::Kilobyte];
        yield 'Negative 1.5 KB' => [(int)(-1.5 * $base), DecimalMemoryUnit::Kilobyte];
        yield 'Negative 1 EB' => [-($base ** 6), DecimalMemoryUnit::Exabyte];
    }
}
