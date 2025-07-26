<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator;

/**
 * @template TKey of array-key
 * @template TValue
 */
interface Arrayable
{
    /**
     * @return array<TKey, TValue>
     */
    public function toArray(): array;
}
