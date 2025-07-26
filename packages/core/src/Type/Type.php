<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Type;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Type
{
    use HasNonInstantiableBehavior;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return T&object
     */
    public static function of(string $type, mixed $value): object
    {
        return $value instanceof $type ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected an instance of %s, but got %s', $type, \get_debug_type($value)),
        );
    }

    /**
     * @phpstan-assert-if-true object|class-string $value
     */
    public static function isClass(mixed $value): bool
    {
        return match (true) {
            \is_object($value) => true,
            \is_string($value) => \class_exists($value),
            default => false,
        };
    }

    /**
     * @phpstan-assert-if-true class-string $value
     */
    public static function isClassString(mixed $value): bool
    {
        return \is_string($value) && (\class_exists($value) || \interface_exists($value));
    }

    /**
     * @template T of object
     * @param class-string<T> $type
     * @phpstan-assert-if-true class-string<T> $value
     */
    public static function isClassStringOf(string $type, mixed $value): bool
    {
        return \is_string($value) && \is_a($value, $type, true);
    }

    /**
     * @phpstan-assert-if-true resource $value
     */
    public static function isStreamResource(mixed $value): bool
    {
        return \is_resource($value) && \get_resource_type($value) === 'stream';
    }

    /**
     * @phpstan-assert-if-true non-empty-string $value
     */
    public static function isNonEmptyString(mixed $value): bool
    {
        return \is_string($value) && $value !== '';
    }

    /**
     * @phpstan-assert non-empty-string $value
     * @return non-empty-string
     */
    public static function ofNonEmptyString(mixed $value): string
    {
        return self::isNonEmptyString($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected a non-empty string, but got %s', self::debug($value)),
        );
    }

    /**
     * @phpstan-assert-if-true non-empty-array<*> $value
     */
    public static function isNonEmptyArray(mixed $value): bool
    {
        return \is_array($value) && $value !== [];
    }

    /**
     * @phpstan-assert-if-true positive-int $value
     */
    public static function isPositiveInt(mixed $value): bool
    {
        return \is_int($value) && $value > 0;
    }

    /**
     * @phpstan-assert positive-int $value
     * @return positive-int
     */
    public static function ofPositiveInt(mixed $value): int
    {
        return self::isPositiveInt($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected a positive int, but got %s', self::debug($value)),
        );
    }

    /**
     * @phpstan-assert-if-true non-negative-int $value
     */
    public static function isNonNegativeInt(mixed $value): bool
    {
        return \is_int($value) && $value >= 0;
    }

    public static function debug(mixed $value): int|string|float
    {
        return match (true) {
            $value === null => 'NULL',
            \is_scalar($value) => match ($value) {
                true => '(bool)TRUE',
                false => '(bool)FALSE',
                default => $value,
            },
            default => \get_debug_type($value), // object, array, etc.
        };
    }

    /**
     * @phpstan-assert-if-true array<*> $value
     * @return array<*>
     */
    public static function ofArray(mixed $value): array
    {
        return \is_array($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected an array, but got %s', self::debug($value)),
        );
    }

    /**
     * @return iterable<*>
     */
    public static function ofIterable(mixed $value): iterable
    {
        return \is_iterable($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected an iterable, but got %s', self::debug($value)),
        );
    }

    public static function ofString(mixed $value): string
    {
        return \is_string($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected a string, but got %s', self::debug($value)),
        );
    }

    public static function ofScalar(mixed $value): int|bool|string|float
    {
        return \is_scalar($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected a scalar, but got %s', self::debug($value)),
        );
    }

    public function ofInt(mixed $value): int
    {
        return \is_int($value) ? $value : throw new \UnexpectedValueException(
            \sprintf('Expected an int, but got %s', self::debug($value)),
        );
    }
}
