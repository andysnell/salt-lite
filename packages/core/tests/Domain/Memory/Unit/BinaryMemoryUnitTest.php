<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Domain\Memory\Unit;

use PhoneBurner\SaltLite\Domain\Memory\Unit\BinaryMemoryUnit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(BinaryMemoryUnit::class)]
final class BinaryMemoryUnitTest extends TestCase
{
    #[Test]
    #[DataProvider('provideCasesWithValueAndSymbol')]
    public function caseHasCorrectValueAndSymbol(
        BinaryMemoryUnit $unit,
        int $expected_value,
        string $expected_symbol,
    ): void {
        self::assertSame($expected_value, $unit->value);
        self::assertSame($expected_symbol, $unit->symbol());
    }

    public static function provideCasesWithValueAndSymbol(): \Generator
    {
        yield 'Byte' => [BinaryMemoryUnit::Byte, 1, 'B'];
        yield 'Kibibyte' => [BinaryMemoryUnit::Kibibyte, 1024, 'KiB'];
        yield 'Mebibyte' => [BinaryMemoryUnit::Mebibyte, 1_048_576, 'MiB'];
        yield 'Gibibyte' => [BinaryMemoryUnit::Gibibyte, 1_073_741_824, 'GiB'];
        yield 'Tebibyte' => [BinaryMemoryUnit::Tebibyte, 1_099_511_627_776, 'TiB'];
        yield 'Pebibyte' => [BinaryMemoryUnit::Pebibyte, 1_125_899_906_842_624, 'PiB'];
        yield 'Exbibyte' => [BinaryMemoryUnit::Exbibyte, 1_152_921_504_606_846_976, 'EiB'];
    }

    #[Test]
    #[DataProvider('provideFitValues')]
    public function fitReturnsCorrectUnit(int $value, BinaryMemoryUnit $expected_unit): void
    {
        self::assertSame($expected_unit, BinaryMemoryUnit::fit($value));
    }

    public static function provideFitValues(): \Generator
    {
        yield 'Zero' => [0, BinaryMemoryUnit::Byte];
        yield 'One Byte' => [1, BinaryMemoryUnit::Byte];
        yield '1023 Bytes' => [1023, BinaryMemoryUnit::Byte];
        yield 'Exactly 1 KiB' => [1024, BinaryMemoryUnit::Kibibyte];
        yield '1.5 KiB' => [(int)(1.5 * 1024), BinaryMemoryUnit::Kibibyte];
        yield 'Exactly 1 MiB' => [1024 ** 2, BinaryMemoryUnit::Mebibyte];
        yield '2.7 MiB' => [(int)(2.7 * 1024 ** 2), BinaryMemoryUnit::Mebibyte];
        yield 'Exactly 1 GiB' => [1024 ** 3, BinaryMemoryUnit::Gibibyte];
        yield 'Exactly 1 TiB' => [1024 ** 4, BinaryMemoryUnit::Tebibyte];
        yield 'Exactly 1 PiB' => [1024 ** 5, BinaryMemoryUnit::Pebibyte];
        yield 'Exactly 1 EiB' => [1024 ** 6, BinaryMemoryUnit::Exbibyte];
        yield 'Negative One Byte' => [-1, BinaryMemoryUnit::Byte];
        yield 'Negative 1 KiB' => [-1024, BinaryMemoryUnit::Kibibyte];
        yield 'Negative 1.5 KiB' => [(int)(-1.5 * 1024), BinaryMemoryUnit::Kibibyte];
        yield 'Negative 1 EiB' => [-1 * (1024 ** 6), BinaryMemoryUnit::Exbibyte];
    }
}
