<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator;

/**
 * @template TKey of array-key
 * @template TValue
 * @implements \IteratorAggregate<TKey, TValue>
 * @implements \ArrayAccess<TKey, TValue>
 * @implements \ArrayAccess<TKey, TValue>
 * @implements Arrayable<TKey, TValue>
 */
class NullableArrayAccess implements \ArrayAccess, \IteratorAggregate, \Countable, Arrayable
{
    /**
     * @param array<TKey, TValue> $array
     */
    public function __construct(private array $array)
    {
    }

    #[\Override]
    public function offsetExists(mixed $offset): bool
    {
        return \array_key_exists($offset, $this->array);
    }

    /**
     * @param TKey $offset
     * @return TValue|null
     */
    #[\ReturnTypeWillChange]
    #[\Override]
    public function offsetGet(mixed $offset)
    {
        return $this->array[$offset] ?? null;
    }

    /**
     * @param TKey|null $offset
     * @param TValue $value
     */
    #[\Override]
    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($offset === null) {
            $this->array[] = $value;
            return;
        }

        $this->array[$offset] = $value;
    }

    #[\Override]
    public function offsetUnset(mixed $offset): void
    {
        unset($this->array[$offset]);
    }

    /**
     * @return \Generator<TKey, TValue>
     */
    #[\Override]
    public function getIterator(): \Generator
    {
        yield from $this->array;
    }

    #[\Override]
    public function count(): int
    {
        return \count($this->array);
    }

    /**
     * @return array<TKey, TValue>
     */
    #[\Override]
    public function toArray(): array
    {
        return $this->array;
    }
}
