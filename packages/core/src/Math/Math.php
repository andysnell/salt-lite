<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Math;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Math
{
    use HasNonInstantiableBehavior;

    /**
     * The PHP builtin \floor() function rounds numbers down to the next lowest
     * integer, but for historic "pre-type-sanity era" reasons, returns a float;
     * however, most places we end up using it want a strict integer typed value.
     */
    public static function floor(int|float $number): int
    {
        return (int)\floor($number);
    }

    /**
     * The PHP builtin \ceil() function rounds numbers down to the next lowest
     * integer, but for historic "pre-type-sanity era" reasons, returns a float;
     * however, most places we end up using it want a strict integer typed value.
     */
    public static function ceil(int|float $number): int
    {
        return (int)\ceil($number);
    }

    public static function iclamp(int|float $value, int $min, int $max): int
    {
        return (int)self::clamp($value, $min, $max);
    }

    public static function fclamp(int|float $value, int|float $min, int|float $max): float
    {
        return (float)self::clamp($value, $min, $max);
    }

    public static function clamp(int|float $value, int|float $min, int|float $max): int|float
    {
        return match (true) {
            $max < $min => throw new \UnexpectedValueException('max must be greater than or equal to min'),
            $value < $min => $min,
            $value > $max => $max,
            default => $value,
        };
    }
}
