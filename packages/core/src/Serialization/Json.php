<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Serialization;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Json
{
    use HasNonInstantiableBehavior;

    public static function encode(mixed $value, int $flags = \JSON_THROW_ON_ERROR): string
    {
        $json = \json_encode($value, $flags);
        if ($json === false) {
            throw new \JsonException('Failed to encode JSON');
        }

        return $json;
    }

    /**
     * @return array<array-key, mixed>
     */
    public static function decode(string $json, int $flags = \JSON_THROW_ON_ERROR): array
    {
        $value = \json_decode($json, true, 512, $flags);
        if (! \is_array($value)) {
            throw new \JsonException('Failed to decode JSON into array');
        }

        return $value;
    }

    public static function validate(string $json, bool $ignore_invalid_utf8_chars = false): bool
    {
        return \json_validate($json, 512, $ignore_invalid_utf8_chars ? \JSON_INVALID_UTF8_IGNORE : 0);
    }
}
