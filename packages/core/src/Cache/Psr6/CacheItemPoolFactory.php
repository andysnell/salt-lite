<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Psr6;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Cache\CacheDriver;
use Psr\Cache\CacheItemPoolInterface;

#[Contract]
interface CacheItemPoolFactory
{
    public function make(CacheDriver $driver, string|null $namespace = null,): CacheItemPoolInterface;
}
