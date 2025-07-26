<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Collections;

/**
 * @template T of object
 * @implements \IteratorAggregate<int, T>
 */
class WeakSet implements \Countable, \IteratorAggregate
{
    /**
     * @var \WeakMap<T, true>
     */
    private \WeakMap $map;

    public function __construct()
    {
        $this->map = new \WeakMap();
    }

    public function has(object $value): bool
    {
        /**
         * @phpstan-ignore argument.type (It's ok to pass an object that isn't T in this method)
         */
        return $this->map->offsetExists($value);
    }

    /**
     * @param T&object $value
     */
    public function add(object $value): void
    {
        $this->map->offsetSet($value, true);
    }

    /**
     * @param T&object $value
     */
    public function remove(object $value): void
    {
        $this->map->offsetUnset($value);
    }

    public function clear(): void
    {
        $this->map = new \WeakMap();
    }

    public function all(): array
    {
        return [...$this->getIterator()];
    }

    public function getIterator(): \Generator
    {
        foreach ($this->map as $value => $_) {
            yield $value;
        }
    }

    public function count(): int
    {
        return $this->map->count();
    }
}
