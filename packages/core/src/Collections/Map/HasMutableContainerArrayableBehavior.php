<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Collections\Map;

use PhoneBurner\SaltLite\Container\MutableContainer;
use PhoneBurner\SaltLite\Iterator\Arrayable;

/**
 * @template TValue
 * @phpstan-require-implements Arrayable
 * @phpstan-require-implements MutableContainer
 */
trait HasMutableContainerArrayableBehavior
{
    public function find(\Stringable|string $key): mixed
    {
        return $this->has($key) ? $this->get($key) : null;
    }

    public function contains(mixed $value, bool $strict = true): bool
    {
        return \in_array($value, $this->toArray(), $strict);
    }

    public function remember(\Stringable|string $key, callable $callback): mixed
    {
        if ($this->has($key)) {
            return $this->get($key);
        }

        $value = $callback();
        $this->set($key, $value);

        return $value;
    }

    public function forget(\Stringable|string $key): mixed
    {
        $value = $this->find($key);
        $this->unset($key);
        return $value;
    }

    /**
     * @template T
     * @param (callable(TValue): T)|(callable(TValue, string): T) $callback
     * @return GenericMapCollection<T> mapping does not necessarily preserve the type of the map implementation
     */
    public function map(callable $callback): GenericMapCollection
    {
        $result = [];
        foreach ($this->toArray() as $key => $value) {
            // Pass both value and key, assuming callback might use the key.
            // If the callback only accepts one argument, PHP handles it gracefully.
            $result[$key] = $callback($value, $key);
        }
        return new GenericMapCollection($result);
    }

    public function filter(callable|null $callback = null): static
    {
        $this->replace(\array_filter($this->toArray(), $callback, \ARRAY_FILTER_USE_BOTH));
        return $this;
    }

    public function reject(callable $callback): static
    {
        return $this->filter(static fn(mixed $value, string $key): bool => ! $callback($value, $key));
    }

    public function all(callable $callback): bool
    {
        return \array_all($this->toArray(), $callback);
    }

    public function any(callable $callback): bool
    {
        return \array_any($this->toArray(), $callback);
    }

    public function count(): int
    {
        return \count($this->toArray());
    }

    /**
     * @return list<string>
     */
    public function keys(): array
    {
        return \array_keys($this->toArray());
    }

    public function getIterator(): \Generator
    {
        yield from $this->toArray();
    }

    public function isEmpty(): bool
    {
        return $this->toArray() === [];
    }
}
