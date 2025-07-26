<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Enum;

/**
 * @phpstan-require-implements \BackedEnum
 */
trait WithStringBackedInstanceStaticMethod
{
    /** Case Insensitive Matching */
    public static function instance(mixed $value): self
    {
        return match (true) {
            $value instanceof self => $value,
            \is_string($value) => self::tryFrom($value)
                ?? \array_find(static::cases(), static fn(self $case): bool => \strcasecmp($case->value, $value) === 0)
                ?? throw new \UnexpectedValueException(),
            \is_int($value), $value instanceof \Stringable => self::instance((string)$value),
            default => throw new \InvalidArgumentException(),
        };
    }

    public static function cast(mixed $value): self|null
    {
        return match (true) {
            $value instanceof self, $value === null => $value,
            \is_string($value) => self::tryFrom($value)
                ?? \array_find(static::cases(), static fn(self $case): bool => \strcasecmp($case->value, $value) === 0),
            \is_int($value), $value instanceof \Stringable => self::cast((string)$value),
            default => null,
        };
    }
}
