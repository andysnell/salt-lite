<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ObjectContainer;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Collections\Map\HasMutableContainerArrayableBehavior;
use PhoneBurner\SaltLite\Collections\Map\HasMutableContainerArrayAccessBehavior;
use PhoneBurner\SaltLite\Collections\MapCollection;
use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PhoneBurner\SaltLite\Container\MutableContainer;
use PhoneBurner\SaltLite\Container\ServiceContainer\HasInvokingContainerBehavior;
use PhoneBurner\SaltLite\Iterator\Arrayable;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template TValue of object
 * @implements MutableContainer<TValue>
 * @implements ObjectContainer<string, TValue>
 * @implements Arrayable<string, TValue>
 */
#[Contract]
class MutableObjectContainer implements MutableContainer, ObjectContainer, Arrayable
{
    use HasInvokingContainerBehavior;
    use HasMutableContainerArrayAccessBehavior;
    /** @use HasMutableContainerArrayableBehavior<TValue> */
    use HasMutableContainerArrayableBehavior;

    /** @param array<string, TValue> $entries */
    public function __construct(protected array $entries = [])
    {
    }

    /**
     * @return TValue&object
     * @throws NotFoundExceptionInterface No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function get(\Stringable|string $id): object
    {
        return $this->entries[(string)$id] ?? throw new NotFound();
    }

    /**
     * @param TValue $value
     */
    public function set(\Stringable|string $id, mixed $value): void
    {
        $this->entries[(string)$id] = $value;
    }

    public function unset(\Stringable|string $id): void
    {
        unset($this->entries[(string)$id]);
    }

    public function has(\Stringable|string $id): bool
    {
        return isset($this->entries[(string)$id]);
    }

    /**
     * @param array<string, TValue>|MapCollection<TValue> $map
     */
    public function replace(array|MapCollection $map): static
    {
        $this->entries = $map instanceof MapCollection ? $map->toArray() : $map;
        return $this;
    }

    public function clear(): void
    {
        $this->entries = [];
    }

    /**
     * @return array<string, TValue>
     */
    public function toArray(): array
    {
        return $this->entries;
    }
}
