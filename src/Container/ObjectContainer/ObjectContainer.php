<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ObjectContainer;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Container\InvokingContainer;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @template TKey of string
 * @template TValue of object
 * @extends \IteratorAggregate<TKey, TValue>
 * @extends \ArrayAccess<TKey, TValue>
 */
#[Contract]
interface ObjectContainer extends InvokingContainer, \Countable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @return TValue&object
     * @throws NotFoundExceptionInterface No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     */
    public function get(string $id): object;

    public function has(string $id): bool;

    /**
     * @return array<string>
     */
    public function keys(): array;
}
