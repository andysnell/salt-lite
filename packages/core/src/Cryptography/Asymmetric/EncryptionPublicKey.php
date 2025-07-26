<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\String\FixedLengthSensitiveBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringImportBehavior;

/**
 * Note: this class is intentionally not readonly, as this allows us to explicitly
 * zero out the key in memory when the object is destroyed.
 */
final class EncryptionPublicKey extends FixedLengthSensitiveBinaryString implements PublicKey
{
    use BinaryStringImportBehavior;

    public const int LENGTH = \SODIUM_CRYPTO_KX_PUBLICKEYBYTES; // 256-bit string

    public function public(): static
    {
        return $this;
    }
}
