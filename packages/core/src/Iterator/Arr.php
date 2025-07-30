<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Arr
{
    use HasNonInstantiableBehavior;

    /**
     * Returns true if the $array is an array primitive or an object that
     * implements `ArrayAccess`. Note that objects that implement `ArrayAccess`
     * are not required to be castable into arrays.
     *
     * @phpstan-assert-if-true array<mixed, mixed>|\ArrayAccess<mixed, mixed> $array
     */
    public static function accessible(mixed $array): bool
    {
        return \is_array($array) || $array instanceof \ArrayAccess;
    }

    /**
     * Returns true if passed an array or an instance of Arrayable or \Traversable.
     *
     * Note: This will return true for \Traversable instances that have keys
     * that are not valid array keys.
     *
     * @phpstan-assert-if-true array<mixed, mixed>|\Traversable<mixed, mixed>|Arrayable<array-key, mixed> $value
     */
    public static function arrayable(mixed $value): bool
    {
        return \is_iterable($value) || $value instanceof Arrayable;
    }

    /**
     * The PHP array_* functions only work with array primitives; however, it is
     * not uncommon to have a $variable that is known to have array-like behavior
     * but not know if it is an array or an instance of Traversable. This method
     * allows for clean conversion without knowing the $value $type. It will
     * return the $value if it is already an array or convert array-like things
     * including instances of iterable or Arrayable. We intentionally do not
     * cast objects as arrays with (array) because the result can be unexpected
     * with the way PHP handles non-public object properties and considering all
     * anonymous functions are actually object instances of \Closure.
     *
     * @param Arrayable<array-key, mixed>|iterable<mixed, mixed> $value
     * @return array<mixed, mixed>
     */
    public static function cast(Arrayable|iterable $value): array
    {
        return match (true) {
            \is_array($value) => $value,
            $value instanceof Arrayable => $value->toArray(),
            default => \iterator_to_array($value),
        };
    }

    /**
     * Return the first value of an iterable value. If the value is an array, the
     * internal array pointer will be not be affected by calling this function.
     * If the value is instead an instance of \Traversable, the internal pointer
     * is reset and exactly one iteration will occur. If the array|iterator is
     * empty, null will be returned.
     *
     * @param iterable<mixed, mixed>|Arrayable<array-key, mixed> $value
     * @return array<mixed, mixed>
     */
    public static function first(iterable|Arrayable $value): mixed
    {
        return match (true) {
            $value === [] => null,
            \is_array($value) => $value[\array_key_first($value)],
            \is_iterable($value) => Iter::first($value),
            default => self::first($value->toArray()),
        };
    }

    /**
     * Return the last element in the iterable $value or null if it is empty.
     * If the value is an array, the internal array pointer will be not be changed
     * by calling this function. If an instance of \Traversable, the entire iterator
     * is consumed to find the last element.
     *
     * @param iterable<mixed, mixed>|Arrayable<array-key, mixed> $value
     * @return array<mixed, mixed>
     */
    public static function last(iterable|Arrayable $value): mixed
    {
        return match (true) {
            $value === [] => null,
            \is_array($value) => $value[\array_key_last($value)],
            \is_iterable($value) => Iter::last($value),
            default => self::last($value->toArray()),
        };
    }

    /**
     * Check if a key is set and has a non-null value from an arbitrary array or
     * object that implements the ArrayAccess interface, supporting dot notation
     * to search a deeply nested array with a composite string key.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<TValue>|\ArrayAccess<TKey, TValue> $array
     */
    public static function has(string $key, array|\ArrayAccess $array): bool
    {
        return self::get($key, $array) !== null;
    }

    /**
     * Check if any of the paths exist in the array or object that implements
     * the ArrayAccess interface, supporting dot notation to search a deeply
     * nested array with a composite string key.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<string> $paths
     * @param array<TValue>|\ArrayAccess<TKey, TValue> $array
     */
    public static function hasAny(array $paths, array|\ArrayAccess $array): bool
    {
        foreach ($paths as $path) {
            if (self::has($path, $array)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if all the paths exist in the array or object that implements
     * the ArrayAccess interface, supporting dot notation to search a deeply
     * nested array with a composite string key.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<string> $paths
     * @param array<TValue>|\ArrayAccess<TKey, TValue> $array
     */
    public function hasAll(array $paths, array|\ArrayAccess $array): bool
    {
        foreach ($paths as $path) {
            if (! self::has($path, $array)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Look up a value from an arbitrary array or object that implements the
     * ArrayAccess interface, supporting dot notation to search a deeply nested
     * array with a composite string key. If the key does not exist or is null,
     * the default value will be returned. If the $default argument is
     * `callable`, it will be evaluated and the result returned.
     *
     * @template TKey of array-key
     * @template TValue
     * @param array<TValue>|\ArrayAccess<TKey, TValue> $array
     * @return TValue|null
     */
    public static function get(string $key, array|\ArrayAccess $array): mixed
    {
        // If the value explicitly exists, even if it has dots, return it early.
        if (isset($array[$key])) {
            return $array[$key];
        }

        foreach (\explode('.', $key) as $subkey) {
            if (! isset($array[$subkey])) {
                return null;
            }
            $array = $array[$subkey];
        }

        return $array;
    }

    public static function dot(array $array): array
    {
        $results = [];
        self::dotFlatten($results, $array, '');
        return $results;
    }

    private static function dotFlatten(array &$results, iterable $array, string $prefix): void
    {
        foreach ($array as $key => $value) {
            $key = $prefix . $key;
            if (\is_iterable($value) && $value) {
                self::dotFlatten($results, $value, $key . '.');
            } else {
                $results[$key] = $value;
            }
        }
    }

    /**
     * Returns the passed value, recursively casting instances of `Arrayable` and
     * `Traversable` into arrays.
     */
    public static function value(mixed $value): mixed
    {
        return self::arrayable($value) ? \array_map(__METHOD__, self::cast($value)) : $value;
    }

    /**
     * If the $value is not an array or an instance of Arrayable or Traversable
     * return the value wrapped in an array, i.e. `[$value]`, otherwise, cast
     * the array, Arrayable or Traversable to an array and return.
     *
     * @return array<mixed, mixed>
     */
    public static function wrap(mixed $value): array
    {
        return self::arrayable($value) ? self::cast($value) : [$value];
    }

    /**
     * @return array<mixed>
     */
    public static function convertNestedObjects(mixed $value): array
    {
        try {
            $encoded = \json_encode($value, \JSON_THROW_ON_ERROR);
            $decoded = \json_decode($encoded, true, 512, \JSON_THROW_ON_ERROR);
            return self::wrap($decoded);
        } catch (\JsonException) {
            return [];
        }
    }

    /**
     * Maps a callback on each element of an iterable, where the first parameter
     * of the callback is the value and the second parameter is the key, returning
     * an array.
     *
     * @template T of array-key
     * @param callable(mixed, T): mixed $callback
     * @param iterable<T, mixed> $iterable
     * @return array<T, mixed>
     */
    public static function map(callable $callback, iterable $iterable): array
    {
        $result = [];
        foreach ($iterable as $key => $value) {
            $result[$key] = $callback($value, $key);
        }

        return $result;
    }
}
