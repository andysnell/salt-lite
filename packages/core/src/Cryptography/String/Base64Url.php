<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\String;

class Base64Url
{
    public static function encode(string $data): string
    {
        return \sodium_bin2base64($data, \SODIUM_BASE64_VARIANT_URLSAFE);
    }

    public static function decode(string $data): string
    {
        return \sodium_base642bin($data, \SODIUM_BASE64_VARIANT_URLSAFE);
    }
}
