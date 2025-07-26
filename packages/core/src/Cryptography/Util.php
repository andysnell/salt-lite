<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography;

use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\BinaryString\PackFormat;

class Util
{
    public static function bytes(\Stringable|BinaryString|string $value): string
    {
        return $value instanceof BinaryString ? $value->bytes() : (string)$value;
    }

    /**
     * Pre-Authentication Encoding
     *
     * Before passing a message to a MAC function, the message must be encoded
     * in a specific way, to prevent certain types of attacks. This encoding is
     * called Pre-Authentication Encoding (PAE). The PAE string is constructed
     * as follows:
     * - The number of parts (as a 64-bit little-endian integer)
     * - For each part:
     *   - The length of the part (as a 64-bit little-endian integer)
     *   - The part itself
     *
     * The PAE string can then be passed to the MAC function as the message. This
     * makes it impossible for an attacker to create a collision with only a
     * partially controlled plaintext or creating an integer overflow.
     *
     * @link https://github.com/paseto-standard/paseto-spec/blob/master/docs/01-Protocol-Versions/Common.md#authentication-padding
     * @return non-empty-string
     **/
    public static function pae(string ...$parts): string
    {
        $accumulator = \pack(PackFormat::INT64_UNSIGNED_LE, \count($parts) & \PHP_INT_MAX);
        foreach ($parts as $string) {
            $accumulator .= \pack(PackFormat::INT64_UNSIGNED_LE, \strlen($string) & \PHP_INT_MAX);
            $accumulator .= $string;
        }

        return $accumulator ?: throw new CryptoLogicException('Accumulator String Cannot Be Empty');
    }
}
