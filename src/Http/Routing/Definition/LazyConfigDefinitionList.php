<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing\Definition;

use Generator;
use IteratorAggregate;
use PhoneBurner\SaltLite\Http\Routing\Definition\DefinitionList;
use PhoneBurner\SaltLite\Http\Routing\Definition\InMemoryDefinitionList;
use PhoneBurner\SaltLite\Http\Routing\Definition\RouteDefinition;
use PhoneBurner\SaltLite\Iterator\Arr;

/**
 * @implements IteratorAggregate<RouteDefinition>
 */
class LazyConfigDefinitionList implements DefinitionList, IteratorAggregate
{
    /**
     * @var array<callable(): Definition|iterable<Definition>>
     */
    private readonly array $callables;

    private InMemoryDefinitionList|null $definition_list = null;

    public function __construct(callable ...$callables)
    {
        $this->callables = $callables;
    }

    public static function makeFromArray(array $route_factories): self
    {
        return new self(...\array_values($route_factories));
    }

    /**
     * @param callable(): Definition ...$callables
     */
    public static function makeFromCallable(callable ...$callables): self
    {
        return new self(...$callables);
    }

    private function getWrapped(): DefinitionList
    {
        return $this->definition_list ??= InMemoryDefinitionList::make(...$this->load());
    }

    /**
     * @return Generator<Definition>
     */
    private function load(): Generator
    {
        foreach ($this->callables as $loader) {
            \assert(\is_callable($loader));
            yield from Arr::wrap($loader());
        }
    }

    #[\Override]
    public function getNamedRoute(string $name): RouteDefinition
    {
        return $this->getWrapped()->getNamedRoute($name);
    }

    #[\Override]
    public function hasNamedRoute(string $name): bool
    {
        return $this->getWrapped()->hasNamedRoute($name);
    }

    #[\Override]
    public function getIterator(): Generator
    {
        yield from $this->getWrapped();
    }
}
