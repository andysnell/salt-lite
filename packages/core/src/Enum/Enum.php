<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Enum;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Enum
{
    use HasNonInstantiableBehavior;

    /**
     * Given a variadic list of enums, return a list of their values.
     *
     * @return array<int|string>
     */
    public static function values(\BackedEnum ...$enum): array
    {
        return \array_column($enum, 'value');
    }
}
