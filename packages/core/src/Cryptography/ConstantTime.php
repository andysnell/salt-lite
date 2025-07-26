<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography;

use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PhoneBurner\SaltLite\String\Encoding\Rfc4648Encoder;

/**
 * Constant-Time Encoding and Operations for Binary Strings
 *
 * A constant-time function is one that takes the same amount of time to execute
 * for any input of a given size. This is important for cryptographic operations
 * because it prevents timing attacks, where an attacker can learn information
 * about the input by measuring how long it takes to process it. All cryptographic
 * operations should use constant-time encoding and comparison functions (e.g.
 * \hash_equals()). This is especially important when working with input from
 * untrusted sources, such as user input or network traffic.
 *
 * @link https://datatracker.ietf.org/doc/html/rfc4648
 * @link https://paragonie.com/blog/2016/06/constant-time-encoding-boring-cryptography-rfc-4648-and-you
 * @see Encoding for the more performant encoding that can be used for non-sensitive data
 */
final class ConstantTime implements Rfc4648Encoder
{
    /**
     * Constant time identity comparison of string-like values. Binary strings
     * are compared byte-wise, with special handling of null input value (i.e.
     * it always returns false, since the known value has to be a string|object).
     */
    public static function equals(
        \Stringable|BinaryString|string $known,
        \Stringable|BinaryString|string|null $input,
    ): bool {
        return $input !== null && \hash_equals(Util::bytes($known), Util::bytes($input));
    }

    public static function stringStartsWith(
        \Stringable|BinaryString|string $haystack,
        \Stringable|BinaryString|string $needle,
    ): bool {
        $needle = Util::bytes($needle);
        $haystack = Util::bytes($haystack);
        return \hash_equals($needle, \substr($haystack, 0, \strlen($needle)));
    }

    /**
     * Constant-time encoding functions for binary strings.
     */
    public static function encode(Encoding $encoding, string $value, bool $prefix = false): string
    {
        return ($prefix ? $encoding->prefix() : '') . match ($encoding) {
            Encoding::Hex => \sodium_bin2hex($value),
            Encoding::Base64 => \sodium_bin2base64($value, \SODIUM_BASE64_VARIANT_ORIGINAL),
            Encoding::Base64NoPadding => \sodium_bin2base64($value, \SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING),
            Encoding::Base64Url => \sodium_bin2base64($value, \SODIUM_BASE64_VARIANT_URLSAFE),
            Encoding::Base64UrlNoPadding => \sodium_bin2base64($value, \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING),
        };
    }

    /**
     * Constant-time decoding functions for binary strings.
     * We support decoding from any of the four base64 variants without needing to
     * know which variant is being used first. We also support decoding from hex
     * and base64 encoded strings with prefixes.
     *
     * @param bool $strict if true, the function will fail if the input does not
     * have the strict expected format for the encoding type.
     */
    public static function decode(Encoding $encoding, string $value, bool $strict = false): string
    {
        if ($strict) {
            try {
                return match ($encoding) {
                    Encoding::Hex => \sodium_hex2bin($value),
                    Encoding::Base64 => \sodium_base642bin($value, \SODIUM_BASE64_VARIANT_ORIGINAL),
                    Encoding::Base64NoPadding => \sodium_base642bin($value, \SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING),
                    Encoding::Base64Url => \sodium_base642bin($value, \SODIUM_BASE64_VARIANT_URLSAFE),
                    Encoding::Base64UrlNoPadding => \sodium_base642bin($value, \SODIUM_BASE64_VARIANT_URLSAFE_NO_PADDING),
                };
            } catch (\Throwable) {
                throw new \UnexpectedValueException('Invalid Encoded String');
            }
        }

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

    private static function decodeFromHex(string $value): string|null
    {
        if (\str_starts_with($value, Encoding::HEX_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::HEX_PREFIX));
        }

        if (\str_starts_with($value, '0x')) {
            $value = \substr($value, 2);
        }

        try {
            return \sodium_hex2bin($value);
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Safely decode a string encoded in any of the four base64 variants without
     * needing to know which variant is being used first. We also safely handle
     *  extra padding characters that may have been added to the end of the string.
     */
    private static function decodeFromBase64(string $value): string|null
    {
        if (\str_starts_with($value, Encoding::BASE64URL_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::BASE64URL_PREFIX));
        } elseif (\str_starts_with($value, Encoding::BASE64_PREFIX)) {
            $value = \substr($value, \strlen(Encoding::BASE64_PREFIX));
        }

        try {
            // Replace URL-safe characters, trim trailing padding, and decode
            return \sodium_base642bin(
                \rtrim(\strtr($value, '-_', '+/'), '='),
                \SODIUM_BASE64_VARIANT_ORIGINAL_NO_PADDING,
            );
        } catch (\Throwable) {
            return null;
        }
    }
}
