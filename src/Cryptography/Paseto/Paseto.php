<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\RegisteredFooterClaim;
use PhoneBurner\SaltLite\Cryptography\Paseto\Exception\PasetoCryptoException;
use PhoneBurner\SaltLite\Serialization\Json;
use PhoneBurner\SaltLite\String\Encoding\Encoding;

/**
 * String (but not BinaryString) wrapper around a Paseto token.
 */
final class Paseto implements \Stringable
{
    public const Encoding ENCODING = Encoding::Base64UrlNoPadding;

    public const string REGEX = '/^(v[1-4])\.(local|public)\.[A-Za-z0-9\-_]+(?:\.([A-Za-z0-9\-_]+))?$/';

    public readonly PasetoVersion $version;

    public readonly PasetoPurpose $purpose;

    public readonly Paserk|null $kid;

    private readonly string $footer;

    public function __construct(
        #[\SensitiveParameter] public readonly string $value,
    ) {
        if (! \preg_match(self::REGEX, $this->value, $matches)) {
            throw new PasetoCryptoException('Invalid PASETO Token');
        }

        $this->version = PasetoVersion::from($matches[1]);
        $this->purpose = PasetoPurpose::from($matches[2]);
        $this->footer = $matches[3] ?? '';
        $this->kid = self::kid($this->version, $this->purpose, $this->footer);
    }

    public function token(): self
    {
        return $this;
    }

    public function footer(): string
    {
        return $this->footer ? ConstantTime::decode(self::ENCODING, $this->footer) : '';
    }

    private static function kid(PasetoVersion $version, PasetoPurpose $purpose, string $footer): Paserk|null
    {
        if ($footer === '') {
            return null;
        }

        try {
            $kid = Json::decode($footer)[RegisteredFooterClaim::KeyId->value] ?? null;
            if ($kid === null) {
                return null;
            }
        } catch (\Throwable) {
            return null;
        }

        $kid = Paserk::import($kid);

        if (
            $kid->type !== match ($purpose) {
                PasetoPurpose::Public => PaserkType::PublicId,
                PasetoPurpose::Local => PaserkType::LocalId,
            }
        ){
            throw new PasetoCryptoException('Invalid PASERK Type for PASETO Purpose');
        }

        if (
            $kid->version !== match ($version) {
                PasetoVersion::V4 => PaserkVersion::V4,
                PasetoVersion::V3 => PaserkVersion::V3,
                PasetoVersion::V2 => PaserkVersion::V2,
                PasetoVersion::V1 => PaserkVersion::V1,
            }
        ){
            throw new PasetoCryptoException('Invalid PASERK Version for PASETO Version');
        }

        return $kid;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
