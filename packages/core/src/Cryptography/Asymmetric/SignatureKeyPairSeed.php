<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\String\FixedLengthSensitiveBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringExportBehavior;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringImportBehavior;

final class SignatureKeyPairSeed extends FixedLengthSensitiveBinaryString
{
    use BinaryStringExportBehavior;
    use BinaryStringImportBehavior;

    public const int LENGTH = \SODIUM_CRYPTO_SIGN_SEEDBYTES; // 32 bytes

    public static function generate(): static
    {
        return new self(\random_bytes(self::LENGTH));
    }
}
