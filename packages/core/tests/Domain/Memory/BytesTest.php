<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Domain\Memory;

use PhoneBurner\SaltLite\Domain\Memory\Bytes;
use PhoneBurner\SaltLite\Domain\Memory\Unit\BinaryMemoryUnit;
use PhoneBurner\SaltLite\Domain\Memory\Unit\DecimalMemoryUnit;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

#[CoversClass(Bytes::class)]
final class BytesTest extends TestCase
{
    #[Test]
    public function constructWithNonNegativeValue(): void
    {
        $bytes = new Bytes(1024);
        self::assertSame(1024, $bytes->value);

        $bytes_zero = new Bytes(0);
        self::assertSame(0, $bytes_zero->value);
    }

    #[Test]
    public function constructThrowsForNegativeValue(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Bytes must be non-negative integer');
        new Bytes(-1);
    }

    #[Test]
    public function bitsReturnsCorrectValue(): void
    {
        $bytes = new Bytes(1);
        self::assertSame(8, $bytes->bits());

        $bytes_large = new Bytes(1024);
        self::assertSame(8192, $bytes_large->bits());

        $bytes_zero = new Bytes(0);
        self::assertSame(0, $bytes_zero->bits());
    }

    #[Test]
    #[DataProvider('provideConversionValues')]
    public function convertReturnsCorrectlyFormattedValue(
        int $byte_value,
        BinaryMemoryUnit|DecimalMemoryUnit $unit,
        int $precision,
        float $expected_value,
    ): void {
        $bytes = new Bytes($byte_value);
        self::assertSame($expected_value, $bytes->convert($unit, $precision));
    }

    public static function provideConversionValues(): \Generator
    {
        yield 'Bytes to KiB (default precision)' => [1536, BinaryMemoryUnit::Kibibyte, 2, 1.5];
        yield 'Bytes to MiB (default precision)' => [1_572_864, BinaryMemoryUnit::Mebibyte, 2, 1.5];
        yield 'Bytes to MiB (0 precision)' => [1_572_864, BinaryMemoryUnit::Mebibyte, 0, 2.0]; // round(1.5)
        yield 'Bytes to GiB (4 precision)' => [1_610_612_736, BinaryMemoryUnit::Gibibyte, 4, 1.5000];
        yield 'Bytes to KB (default precision)' => [1500, DecimalMemoryUnit::Kilobyte, 2, 1.5];
        yield 'Bytes to MB (1 precision)' => [1_500_000, DecimalMemoryUnit::Megabyte, 1, 1.5];
        yield 'Zero Bytes to KiB' => [0, BinaryMemoryUnit::Kibibyte, 2, 0.0];
        yield 'Default Unit (MiB)' => [(int)(2.5 * BinaryMemoryUnit::Mebibyte->value), BinaryMemoryUnit::Mebibyte, 2, 2.50];
    }

    #[Test]
    public function jsonSerializeReturnsIntegerValue(): void
    {
        $bytes = new Bytes(512);
        self::assertSame(512, $bytes->jsonSerialize());
    }

    #[Test]
    #[DataProvider('provideToStringValues')]
    public function toStringReturnsFormattedStringWithBestFitBinaryUnit(
        int $value,
        string $expected_string,
    ): void {
        $bytes = new Bytes($value);
        self::assertSame($expected_string, (string)$bytes);
    }

    public static function provideToStringValues(): \Generator
    {
        yield 'Zero Bytes' => [0, '0.00 B'];
        yield '500 Bytes' => [500, '500.00 B'];
        yield '1024 Bytes (1 KiB)' => [1024, '1.00 KiB'];
        yield '1536 Bytes (1.5 KiB)' => [1536, '1.50 KiB'];
        yield '1 MiB' => [1024 ** 2, '1.00 MiB'];
        yield '1.25 MiB' => [(int)(1.25 * (1024 ** 2)), '1.25 MiB'];
        yield 'Large Value (GiB)' => [(int)(3.7 * (1024 ** 3)), '3.70 GiB'];
    }
}
