<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\BinaryString\Traits;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\Encoding\Encoding;

/**
 * @phpstan-require-implements BinaryString
 */
trait BinaryStringExportBehavior
{
    public function export(
        Encoding|null $encoding = null,
        bool $prefix = false,
    ): string {
        return ConstantTime::encode($encoding ?? static::DEFAULT_ENCODING, $this->bytes(), $prefix);
    }
}
