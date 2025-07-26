<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Exception;

class InvalidKeySeed extends CryptoRuntimeException
{
    public static function length(int $expected): self
    {
        return new self(\sprintf("Key Seed Must Be Exactly %d Bytes", $expected));
    }
}
