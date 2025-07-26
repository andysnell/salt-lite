<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Protocol;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignatureKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignaturePublicKey;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\PasetoMessage;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paseto;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;

interface PasetoProtocol
{
    public static function encrypt(
        SharedKey $key,
        PasetoMessage $message,
        string $additional_data = '',
    ): Paseto;

    public static function decrypt(
        SharedKey $key,
        Paseto $token,
        string $additional_data = '',
    ): PasetoMessage;

    public static function sign(
        SignatureKeyPair $key_pair,
        PasetoMessage $message,
        string $additional_data = '',
    ): Paseto;

    public static function verify(
        SignaturePublicKey $key,
        Paseto $token,
        string $additional_data = '',
    ): PasetoMessage;
}
