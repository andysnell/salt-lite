<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\Encoding;

/**
 * String Data Encoding Variants (RFC 4648)
 *
 * @link https://datatracker.ietf.org/doc/html/rfc4648
 */
enum Encoding
{
    case Hex;
    case Base64;
    case Base64NoPadding;
    case Base64Url;
    case Base64UrlNoPadding;

    public const string HEX_PREFIX = 'hex:';
    public const string BASE64_PREFIX = 'base64:';
    public const string BASE64URL_PREFIX = 'base64url:';

    public const string HEX_REGEX = '/^[A-Fa-f0-9]+$/';
    public const string BASE64_REGEX = '/^[A-Za-z0-9+\/]+={0,2}$/';
    public const string BASE64URL_REGEX = '/^[A-Za-z0-9-_]+={0,2}$/';
    public const string BASE64_NO_PADDING_REGEX = '/^[A-Za-z0-9+\/]+$/';
    public const string BASE64URL_NO_PADDING_REGEX = '/^[A-Za-z0-9-_]+$/';

    /**
     * Note that we have to use a different prefix for base64url encoding, even
     * though we decode both base64 and base64url encoded strings in the same way
     * in order to be compliant with RFC 4648, which requires that the two encodings
     * not be conflated together as 'base64'.
     *
     * @link https://datatracker.ietf.org/doc/html/rfc4648#section-5
     */
    public function prefix(): string
    {
        return match ($this) {
            self::Hex => self::HEX_PREFIX,
            self::Base64, self::Base64NoPadding => self::BASE64_PREFIX,
            self::Base64Url, self::Base64UrlNoPadding => self::BASE64URL_PREFIX,
        };
    }

    public function regex(): string
    {
        return match ($this) {
            self::Hex => self::HEX_REGEX,
            self::Base64 => self::BASE64_REGEX,
            self::Base64NoPadding => self::BASE64_NO_PADDING_REGEX,
            self::Base64Url => self::BASE64URL_REGEX,
            self::Base64UrlNoPadding => self::BASE64URL_NO_PADDING_REGEX,
        };
    }
}
