<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\Memory\Unit;

use PhoneBurner\SaltLite\Math\Math;

enum BinaryMemoryUnit: int
{
    case Byte = self::BASE ** 0;
    case Kibibyte = self::BASE ** 1;
    case Mebibyte = self::BASE ** 2;
    case Gibibyte = self::BASE ** 3;
    case Tebibyte = self::BASE ** 4;
    case Pebibyte = self::BASE ** 5;
    case Exbibyte = self::BASE ** 6;

    public const int BASE = 2 ** 10;

    public function symbol(): string
    {
        return match ($this) {
            self::Byte => 'B',
            self::Kibibyte => 'KiB',
            self::Mebibyte => 'MiB',
            self::Gibibyte => 'GiB',
            self::Tebibyte => 'TiB',
            self::Pebibyte => 'PiB',
            self::Exbibyte => 'EiB',
        };
    }

    public static function fit(int $value): self
    {
        return $value === 0 ? self::Byte : match (Math::floor(\log(\abs($value), self::BASE))) {
            0 => self::Byte,
            1 => self::Kibibyte,
            2 => self::Mebibyte,
            3 => self::Gibibyte,
            4 => self::Tebibyte,
            5 => self::Pebibyte,
            default => self::Exbibyte,
        };
    }
}
