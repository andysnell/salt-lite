<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ObjectContainer;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PhoneBurner\SaltLite\Container\ObjectContainer\ObjectContainer;
use PhoneBurner\SaltLite\Container\ServiceContainer\HasInvokingContainerBehavior;
use PhoneBurner\SaltLite\Exception\InvalidStringableOffset;
use PhoneBurner\SaltLite\String\Str;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template TValue of object
 * @implements ObjectContainer<string, TValue>
 */
#[Contract]
final readonly class ImmutableObjectContainer implements ObjectContainer
{
    use HasInvokingContainerBehavior;

    /**
     * @param array<string, TValue> $entries
     */
    public function __construct(private array $entries)
    {
    }

    /**
     * @return TValue&object
     * @throws NotFoundExceptionInterface No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function get(string $id): object
    {
        return $this->entries[$id] ?? throw new NotFound();
    }

    public function has(string $id): bool
    {
        return isset($this->entries[$id]);
    }

    /**
     * @return array<string>
     */
    public function keys(): array
    {
        return \array_keys($this->entries);
    }

    public function getIterator(): \Generator
    {
        yield from $this->entries;
    }

    public function count(): int
    {
        return \count($this->entries);
    }

    public function offsetExists(mixed $offset): bool
    {
        return Str::stringable($offset) && $this->has((string)$offset);
    }

    /**
     * @return TValue&object
     */
    public function offsetGet(mixed $offset): object
    {
        Str::stringable($offset) || throw new InvalidStringableOffset($offset);
        return $this->entries[$offset] ?? throw new NotFound();
    }

    public function offsetSet(mixed $offset, mixed $value): never
    {
        throw new \LogicException('Container is Immutable and Readonly');
    }

    public function offsetUnset(mixed $offset): never
    {
        throw new \LogicException('Container is Immutable and Readonly');
    }
}
