<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Type\Cast;

use Carbon\CarbonImmutable;
use Carbon\Exceptions\Exception as CarbonException;
use PhoneBurner\SaltLite\Time\Standards\AnsiSql;
use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class NullableCast
{
    use HasNonInstantiableBehavior;

    public static function integer(mixed $value): int|null
    {
        return match (true) {
            \is_int($value), $value === null => $value,
            \is_scalar($value) => (int)$value,
            $value instanceof \BackedEnum => (int)$value->value,
            default => throw new \TypeError('Invalid type for integer cast: ' . \gettype($value)),
        };
    }

    public static function float(mixed $value): float|null
    {
        return match (true) {
            \is_float($value), $value === null => $value,
            \is_scalar($value) => (float)$value,
            $value instanceof \BackedEnum => (float)$value->value,
            default => throw new \TypeError('Invalid type for float cast: ' . \gettype($value)),
        };
    }

    /**
     * @return ($value is null ? null : string)
     */
    public static function string(mixed $value): string|null
    {
        return match (true) {
            \is_string($value), $value === null => $value,
            \is_scalar($value), $value instanceof \Stringable => (string)$value,
            $value instanceof \BackedEnum => (string)$value->value,
            default => throw new \TypeError('Invalid type for string cast: ' . \gettype($value)),
        };
    }

    public static function boolean(mixed $value): bool|null
    {
        return $value !== null ? (bool)$value : null;
    }

    public static function datetime(mixed $value): CarbonImmutable|null
    {
        try {
            return match (true) {
                $value instanceof CarbonImmutable, $value === null => $value,
                $value === AnsiSql::NULL_DATETIME, $value === '' => null,
                $value instanceof \DateTimeInterface, \is_string($value) => CarbonImmutable::make($value),
                \is_int($value) => CarbonImmutable::createFromTimestamp($value),
                default => throw new \TypeError('Invalid type for datetime cast: ' . \gettype($value)),
            };
        } catch (CarbonException) {
            return null;
        }
    }
}
