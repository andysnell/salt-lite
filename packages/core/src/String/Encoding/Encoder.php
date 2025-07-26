<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\Encoding;

use function PhoneBurner\SaltLite\null_if_false;

final class Encoder implements Rfc4648Encoder
{
    public static function encode(Encoding $encoding, string $value, bool $prefix = false): string
    {
        return ($prefix ? $encoding->prefix() : '') . match ($encoding) {
            Encoding::Hex => \bin2hex($value),
            Encoding::Base64 => \base64_encode($value),
            Encoding::Base64NoPadding => \trim(\base64_encode($value), '='),
            Encoding::Base64Url => \strtr(\base64_encode($value), '+/', '-_'),
            Encoding::Base64UrlNoPadding => \trim(\strtr(\base64_encode($value), '+/', '-_'), '='),
        };
    }

    public static function decode(Encoding $encoding, string $value): string
    {
        return match ($encoding) {
            Encoding::Hex => self::decodeFromHex($value) ?? throw new \UnexpectedValueException(
                'Invalid Hex Encoded String',
            ),
            Encoding::Base64, Encoding::Base64NoPadding => self::decodeFromBase64($value) ?? throw new \UnexpectedValueException(
                'Invalid Base64 Encoded String',
            ),
            Encoding::Base64Url, Encoding::Base64UrlNoPadding => self::decodeFromBase64($value) ?? throw new \UnexpectedValueException(
                'Invalid Base64Url Encoded String',
            ),
        };
    }

    public static function tryDecode(Encoding $encoding, string $value): string|null
    {
        try {
            return self::decode($encoding, $value);
        } catch (\Throwable) {
            return null;
        }
    }

    private static function decodeFromHex(string $value): string|null
    {
        if (\str_starts_with($value, Encoding::HEX_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::HEX_PREFIX));
        }

        if (\str_starts_with($value, '0x')) {
            $value = \substr($value, 2);
        }

        // Intentionally suppress the \E_WARNING that is also raised (in addition
        // to the boolean false return value) when the input is not a valid hex string
        return null_if_false(@\hex2bin($value));
    }

    /**
     * Safely decode a string encoded in any of the four base64 variants without
     * needing to know which variant is being used first. We also safely handle
     * extra padding characters that may have been added to the end of the string.
     */
    private static function decodeFromBase64(string $value): string|null
    {
        if (\str_starts_with($value, Encoding::BASE64URL_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::BASE64URL_PREFIX));
        } elseif (\str_starts_with($value, Encoding::BASE64_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::BASE64_PREFIX));
        }

        // Replace URL-safe characters, and trim trailing padding)
        $value = \rtrim(\strtr($value, '-_', '+/'), '=');

        // Calculate the correct padding length based on the length of the input string
        return null_if_false(match (\strlen($value) % 4) {
            0 => \base64_decode(\strtr($value, '-_', '+/'), true),
            2 => \base64_decode(\strtr($value, '-_', '+/') . '==', true),
            3 => \base64_decode(\strtr($value, '-_', '+/') . '=', true),
            default => null, // Valid base64 strings cannot have a length that is 1 mod 4
        });
    }
}
