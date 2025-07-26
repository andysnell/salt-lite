<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

/**
 * Helper class for working with iterators.
 *
 * @see \PhoneBurner\SaltLite\Iterator\Arr for working with arrays.
 */
final readonly class Iter
{
    use HasNonInstantiableBehavior;

    /**
     * The `iterable` pseudotype is the union of `array|Traversable`, and can be
     * used for both parameter and return typing; however, almost all the
     * PHP functions for working with iterable things will only accept `array`
     * or a `Traversable` object. We commonly need one or the other, and by type
     * hinting on `iterable`, we don't know at runtime what we are working with.
     * This helper method takes any iterable and returns an `Iterator`.
     * This also works with any class that implements Arrayable. If an object is
     * an instance of both `Traversable` and `Arrayable`, the method returns the
     * object like other `Traversable` objects.
     *
     * @template T
     * @param Arrayable<array-key, T>|iterable<T> $value
     * @return \Iterator<T>
     */
    public static function cast(Arrayable|iterable $value): \Iterator
    {
        return match (true) {
            \is_array($value) => new \ArrayIterator($value),
            $value instanceof \Iterator => $value,
            $value instanceof \Traversable => new \IteratorIterator($value),
            $value instanceof Arrayable => new \ArrayIterator($value->toArray()),
        };
    }

    /**
     * @template T
     * @param iterable<T> $iter
     * @return T|null
     */
    public static function first(iterable $iter): mixed
    {
        foreach ($iter as $value) {
            return $value;
        }

        return null;
    }

    /**
     * @template T
     * @param iterable<T> $iter
     * @return T|null
     */
    public static function last(iterable $iter): mixed
    {
        $last = null;
        foreach ($iter as $value) {
            $last = $value;
        }

        return $last;
    }

    /**
     * Maps a callback on each element of an iterable, where the first parameter
     * of the callback is the value and the second parameter is the key.
     *
     * @template T
     * @template TKey of int|string
     * @template TValue
     * @param callable(T, TKey): TValue $callback
     * @param iterable<TKey, T> $iter
     * @return \Generator<TKey, TValue>
     */
    public static function map(callable $callback, iterable $iter): \Generator
    {
        foreach ($iter as $key => $value) {
            yield $key => $callback($value, $key);
        }
    }

    /**
     * Maps an iterable to an array via a callback
     *
     * @template T
     * @template TKey of int|string
     * @template TValue
     * @param callable(T): TValue $callback
     * @param iterable<TKey, T> $iter
     * @return array<TKey, TValue>
     */
    public static function amap(callable $callback, iterable $iter): array
    {
        $result = [];
        foreach ($iter as $key => $value) {
            $result[$key] = $callback($value);
        }
        return $result;
    }

    /**
     * @param iterable<mixed>|Arrayable<array-key, mixed> ...$iterables
     * @return \AppendIterator<mixed, mixed, \Iterator<mixed>>
     */
    public static function chain(iterable|Arrayable ...$iterables): \AppendIterator
    {
        $append_iterator = new \AppendIterator();
        foreach ($iterables as $iter) {
            $append_iterator->append(self::cast($iter));
        }

        return $append_iterator;
    }

    /**
     * @template TKey
     * @template TValue
     * @param iterable<TKey, TValue> $iter
     * @return \Generator<TKey, TValue>
     */
    public static function generate(iterable $iter): \Generator
    {
        yield from $iter;
    }
}
