<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\BinaryString;

use PhoneBurner\SaltLite\String\Encoding\Encoding;

/**
 * Represents a some kind of string in its raw binary form, something like a
 * cryptographic key, message signature, or ciphertext. Usually this would be
 * something where we are usually concerned with the raw bytes of the string and
 * not a human-readable representation. Encapsulating the raw bytes in a class
 * allows us to enforce type safety and avoid accidentally mixing up different
 * type of binary strings, or accidentally treating a binary string as a regular
 * string in a context where that would be inappropriate.
 */
interface BinaryString extends \Stringable, \JsonSerializable
{
    public const Encoding DEFAULT_ENCODING = Encoding::Base64Url;

    /**
     * @return string The raw binary string material
     */
    public function bytes(): string;

    /**
     * @return non-negative-int The length of the binary string in bytes
     */
    public function length(): int;

    /**
     * Return the binary string as an encoded string
     *
     * @param bool $prefix If true, the encoded string will be prefixed an
     * identifier for the encoding type, either "base64:", "base64url", or "hex:".
     */
    public function export(
        Encoding|null $encoding = null,
        bool $prefix = false,
    ): string;
}
