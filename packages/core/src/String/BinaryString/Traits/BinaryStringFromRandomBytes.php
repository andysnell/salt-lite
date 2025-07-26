<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\BinaryString\Traits;

use PhoneBurner\SaltLite\Cryptography\String\FixedLengthSensitiveBinaryString;

/**
 * @phpstan-require-extends FixedLengthSensitiveBinaryString
 */
trait BinaryStringFromRandomBytes
{
    public static function generate(): self
    {
        return new self(\random_bytes(self::LENGTH));
    }
}
