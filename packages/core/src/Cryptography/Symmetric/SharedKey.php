<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Symmetric;

use PhoneBurner\SaltLite\Cryptography\KeyManagement\Key;
use PhoneBurner\SaltLite\Cryptography\String\FixedLengthSensitiveBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringFromRandomBytes;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringImportBehavior;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringProhibitsSerialization;

/**
 * 256-bit symmetric key for use with XChaCha20 or AEGIS-256 ciphers
 *
 * Note: this class is intentionally not readonly, as this allows us to explicitly
 * zero out the key in memory when the object is destroyed.
 */
final class SharedKey extends FixedLengthSensitiveBinaryString implements Key
{
    use BinaryStringProhibitsSerialization;
    use BinaryStringFromRandomBytes;
    use BinaryStringImportBehavior;

    public const int LENGTH = \SODIUM_CRYPTO_STREAM_XCHACHA20_KEYBYTES;
}
