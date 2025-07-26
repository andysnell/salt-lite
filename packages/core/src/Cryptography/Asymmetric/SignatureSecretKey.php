<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\String\FixedLengthSensitiveBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringImportBehavior;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringProhibitsSerialization;

/**
 * Note: this class is intentionally not readonly, as this allows us to explicitly
 * zero out the key in memory when the object is destroyed.
 */
final class SignatureSecretKey extends FixedLengthSensitiveBinaryString implements SecretKey
{
    use BinaryStringProhibitsSerialization;
    use BinaryStringImportBehavior;

    public const int LENGTH = \SODIUM_CRYPTO_SIGN_SECRETKEYBYTES; // 512-bit string

    public function secret(): static
    {
        return $this;
    }
}
