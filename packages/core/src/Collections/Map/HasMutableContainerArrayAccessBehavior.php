<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Collections\Map;

use PhoneBurner\SaltLite\Container\MutableContainer;
use PhoneBurner\SaltLite\Exception\InvalidStringableOffset;
use PhoneBurner\SaltLite\String\Str;

/**
 * @phpstan-require-implements \ArrayAccess
 * @phpstan-require-implements MutableContainer
 */
trait HasMutableContainerArrayAccessBehavior
{
    public function offsetExists(mixed $offset): bool
    {
        return Str::stringable($offset) && $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        Str::stringable($offset) || throw new InvalidStringableOffset($offset);
        /** @phpstan-ignore return.type */
        return $this->has($offset) ? $this->get($offset) : null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        Str::stringable($offset) || throw new InvalidStringableOffset($offset);
        $this->set((string)$offset, $value);
    }

    public function offsetUnset(mixed $offset): void
    {
        Str::stringable($offset) || throw new InvalidStringableOffset($offset);
        $this->unset($offset);
    }
}
