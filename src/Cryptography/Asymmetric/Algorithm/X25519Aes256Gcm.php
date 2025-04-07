<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric\Algorithm;

use PhoneBurner\SaltLite\Attribute\Usage\Internal;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\Asymmetric;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricEncryptionAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionPublicKey;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\KeyExchange;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\Message\EncryptedMessageBox;
use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PhoneBurner\SaltLite\Cryptography\Exception\UnsupportedOperation;
use PhoneBurner\SaltLite\Cryptography\String\BinaryString;
use PhoneBurner\SaltLite\Cryptography\Symmetric\Algorithm\Aes256Gcm;

/**
 * Diffie-Hellman key exchange over Curve25519 + AES-256-GCM AEAD
 *
 * @see Aes256Gcm for more information on the encryption algorithm details
 */
#[Internal('Client Code Should Not Use Algorithm Implementation', Asymmetric::class)]
final readonly class X25519Aes256Gcm implements AsymmetricEncryptionAlgorithm
{
    use HasCommonAnonymousEncryptionBehavior;

    public const int KEY_PAIR_BYTES = \SODIUM_CRYPTO_KX_KEYPAIRBYTES; // 64 bytes
    public const int PUBLIC_KEY_BYTES = \SODIUM_CRYPTO_KX_PUBLICKEYBYTES; // 32 bytes
    public const int SECRET_KEY_BYTES = \SODIUM_CRYPTO_KX_SECRETKEYBYTES; // 32 bytes

    public static function encrypt(
        #[\SensitiveParameter] EncryptionKeyPair $key_pair,
        #[\SensitiveParameter] EncryptionPublicKey $public_key,
        #[\SensitiveParameter] string $plaintext,
        #[\SensitiveParameter] string $additional_data = '',
    ): EncryptedMessageBox {
        Aes256Gcm::available() || throw new CryptoLogicException(
            'AES-256-GCM is not available on this system',
        );

        $encrypted_message = Aes256Gcm::encrypt(
            KeyExchange::encryption($key_pair, $public_key),
            $plaintext,
            $additional_data,
        );

        return new EncryptedMessageBox(
            AsymmetricAlgorithm::X25519Aes256Gcm,
            $key_pair->public,
            $public_key,
            $encrypted_message->ciphertext,
            $encrypted_message->nonce,
        );
    }

    public static function decrypt(
        #[\SensitiveParameter] EncryptionKeyPair $key_pair,
        #[\SensitiveParameter] EncryptionPublicKey $public_key,
        #[\SensitiveParameter] BinaryString $ciphertext,
        #[\SensitiveParameter] string $additional_data = '',
    ): string|null {
        Aes256Gcm::available() || throw new CryptoLogicException(
            'AES-256-GCM is not available on this system',
        );

        return Aes256Gcm::decrypt(
            KeyExchange::decryption($key_pair, $public_key),
            $ciphertext,
            $additional_data,
        );
    }

    public static function seal(
        #[\SensitiveParameter] EncryptionPublicKey $public_key,
        #[\SensitiveParameter] string $plaintext,
        #[\SensitiveParameter] string $additional_data = '',
    ): never {
        throw new UnsupportedOperation('Sealing is not supported with AES-256-GCM (Weak Nonce Length)');
    }

    public static function unseal(
        #[\SensitiveParameter] EncryptionKeyPair $key_pair,
        #[\SensitiveParameter] BinaryString $ciphertext,
        #[\SensitiveParameter] string $additional_data = '',
    ): never {
        throw new UnsupportedOperation('Sealing is not supported with AES-256-GCM (Weak Nonce Length)');
    }
}
