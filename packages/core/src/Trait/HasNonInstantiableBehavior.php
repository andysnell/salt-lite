<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Trait;

use PhoneBurner\SaltLite\Exception\NotInstantiable;

trait HasNonInstantiableBehavior
{
    final public function __construct()
    {
        throw new NotInstantiable(self::class);
    }
}
