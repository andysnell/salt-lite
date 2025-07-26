<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Collections\Map;

use PhoneBurner\SaltLite\Collections\MapCollection;

/**
 * @template TValue
 * @phpstan-require-implements MapCollection
 */
trait MapWrapper
{
    use HasMutableContainerArrayAccessBehavior;
    /** @use HasMutableContainerArrayableBehavior<TValue> */
    use HasMutableContainerArrayableBehavior;

    abstract private function wrapped(): MapCollection;

    public function has(\Stringable|string $key): bool
    {
        return $this->wrapped()->has($key);
    }

    public function get(\Stringable|string $key): mixed
    {
        return $this->wrapped()->get($key);
    }

    public function find(\Stringable|string $key): mixed
    {
        return $this->wrapped()->find($key);
    }

    public function set(\Stringable|string $key, mixed $value): void
    {
        $this->wrapped()->set($key, $value);
    }

    public function unset(\Stringable|string $key): void
    {
        $this->wrapped()->unset($key);
    }

    public function replace(MapCollection|array $map): static
    {
        $this->wrapped()->replace($map);
        return $this;
    }

    public function clear(): void
    {
        $this->wrapped()->clear();
    }

    /**
     * @return array<string, TValue>
     */
    public function toArray(): array
    {
        return $this->wrapped()->toArray();
    }
}
