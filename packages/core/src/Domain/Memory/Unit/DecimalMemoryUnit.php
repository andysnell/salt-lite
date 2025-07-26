<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\Memory\Unit;

use PhoneBurner\SaltLite\Math\Math;

enum DecimalMemoryUnit: int
{
    case Byte = self::BASE ** 0;
    case Kilobyte = self::BASE ** 1;
    case Megabyte = self::BASE ** 2;
    case Gigabyte = self::BASE ** 3;
    case Terabyte = self::BASE ** 4;
    case Petabyte = self::BASE ** 5;
    case Exabyte = self::BASE ** 6;

    public const int BASE = 10 ** 3;

    public function symbol(): string
    {
        return match ($this) {
            self::Byte => 'B',
            self::Kilobyte => 'KB',
            self::Megabyte => 'MB',
            self::Gigabyte => 'GB',
            self::Terabyte => 'TB',
            self::Petabyte => 'PB',
            self::Exabyte => 'EB',
        };
    }

    public static function fit(int $value): self
    {
        return $value === 0 ? self::Byte : match (Math::floor(\log(\abs($value), self::BASE))) {
            0 => self::Byte,
            1 => self::Kilobyte,
            2 => self::Megabyte,
            3 => self::Gigabyte,
            4 => self::Terabyte,
            5 => self::Petabyte,
            default => self::Exabyte,
        };
    }
}
