<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Enum;

/**
 * @phpstan-require-implements \BackedEnum
 */
trait WithIntegerBackedInstanceStaticMethod
{
    public static function instance(mixed $value): self
    {
        return match (true) {
            $value instanceof self => $value,
            \is_numeric($value) => self::tryFrom((int)$value) ?? throw new \UnexpectedValueException(),
            default => throw new \InvalidArgumentException(),
        };
    }

    public static function cast(mixed $value): self|null
    {
        return match (true) {
            $value instanceof self, $value === null => $value,
            \is_numeric($value) => self::tryFrom((int)$value),
            default => null,
        };
    }
}
