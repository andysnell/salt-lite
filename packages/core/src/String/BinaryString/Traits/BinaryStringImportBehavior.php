<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\BinaryString\Traits;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\String\BinaryString\ImportableBinaryString;
use PhoneBurner\SaltLite\String\Encoding\Encoding;

/**
 * @phpstan-require-implements ImportableBinaryString
 */
trait BinaryStringImportBehavior
{
    public static function import(
        #[\SensitiveParameter] string $string,
        Encoding|null $encoding = null,
    ): static {
        return new static(ConstantTime::decode($encoding ?? static::DEFAULT_ENCODING, $string));
    }

    public static function tryImport(
        #[\SensitiveParameter] string|null $string,
        Encoding|null $encoding = null,
    ): static|null {
        try {
            return $string ? static::import($string, $encoding) : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
