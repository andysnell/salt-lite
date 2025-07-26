<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Symmetric;

use PhoneBurner\SaltLite\String\BinaryString\BinaryString;

interface SymmetricEncryptionAlgorithm
{
    public const int KEY_BYTES = \SODIUM_CRYPTO_STREAM_XCHACHA20_KEYBYTES;
    public const int NONCE_BYTES = \SODIUM_CRYPTO_STREAM_XCHACHA20_NONCEBYTES;

    public static function encrypt(
        #[\SensitiveParameter] SharedKey $key,
        #[\SensitiveParameter] string $plaintext,
        #[\SensitiveParameter] string $additional_data = '',
    ): EncryptedMessage;

    public static function decrypt(
        #[\SensitiveParameter] SharedKey $key,
        #[\SensitiveParameter] BinaryString $ciphertext,
        #[\SensitiveParameter] string $additional_data = '',
    ): string|null;
}
