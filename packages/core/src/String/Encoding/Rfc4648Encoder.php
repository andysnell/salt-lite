<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\Encoding;

interface Rfc4648Encoder
{
    public static function encode(Encoding $encoding, string $value, bool $prefix = false): string;

    public static function decode(Encoding $encoding, string $value): string;
}
