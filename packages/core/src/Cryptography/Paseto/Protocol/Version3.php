<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Protocol;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignatureKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignaturePublicKey;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\PasetoMessage;
use PhoneBurner\SaltLite\Cryptography\Paseto\Exception\PasetoCryptoException;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paseto;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoVersion;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;

/**
 * Version 3: NIST Modern
 */
class Version3 implements PasetoProtocol
{
    public const PasetoVersion VERSION = PasetoVersion::V3;
    public const string HEADER_PUBLIC = PasetoVersion::V3->value . '.public.';
    public const string HEADER_LOCAL = PasetoVersion::V3->value . '.local.';

    public static function encrypt(
        SharedKey $key,
        PasetoMessage $message,
        string $additional_data = '',
    ): Paseto {
        throw new PasetoCryptoException('Unsupported Paseto Protocol Version');
    }

    public static function decrypt(
        SharedKey $key,
        Paseto $token,
        string $additional_data = '',
    ): PasetoMessage {
        throw new PasetoCryptoException('Unsupported Paseto Protocol Version');
    }

    public static function sign(
        SignatureKeyPair $key_pair,
        PasetoMessage $message,
        string $additional_data = '',
    ): Paseto {
        throw new PasetoCryptoException('Unsupported Paseto Protocol Version');
    }

    public static function verify(
        SignaturePublicKey $key,
        Paseto $token,
        string $additional_data = '',
    ): PasetoMessage {
        throw new PasetoCryptoException('Unsupported Paseto Protocol Version');
    }
}
