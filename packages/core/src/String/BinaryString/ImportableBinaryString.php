<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\BinaryString;

use PhoneBurner\SaltLite\String\Encoding\Encoding;

interface ImportableBinaryString extends BinaryString
{
    public function __construct(#[\SensitiveParameter] BinaryString|string $bytes);

    /**
     * Create a new instance of the binary string from a hex-encoded string, or
     * a string encoded with one of the four base64 variants supported by
     * \sodium_bin2base64().
     *
     * Hex Encoding: "hex:0123456789abcdef" or "0x0123456789abcdef" or "0123456789abcdef"
     * - Implementations MUST ignore the "hex:" prefix for hex encoded strings
     * - Implementations MUST ignore leading "0x" characters
     * - Implementations MUST treat hex strings in a case-insensitive manner
     *
     * Base64/Base64Url Encoding: "base64:SGVsbG8gV29ybGQ=" or "base64url:SGVsbG8gV29ybGQ="
     * - Implementations MUST ignore the "base64:" and "base64url:" prefixes
     * - Implementations MUST ignore extra or missing trailing padding and calculate the correct
     * - Implementations MUST treat base64 and base64url as equivalent encodings
     *
     * Optionally, implementations with a fixed length MAY also enforce that the
     * decoded binary string match that length.
     */
    public static function import(
        #[\SensitiveParameter] string $string,
        Encoding|null $encoding = null,
    ): static;

    /**
     * Follows the same rules as the import() method, but has a more open signature
     * and returns null instead of throwing an exception if the input string is
     * not a valid encoding.
     */
    public static function tryImport(
        #[\SensitiveParameter] string|null $string,
        Encoding|null $encoding = null,
    ): static|null;
}
