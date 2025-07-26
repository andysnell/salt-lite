<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache;

use PhoneBurner\SaltLite\Cache\Psr6\InMemoryCachePool;
use Psr\Cache\CacheItemPoolInterface;

final class InMemoryCache extends CacheAdapter
{
    public function __construct(CacheItemPoolInterface $cache_item_pool = new InMemoryCachePool())
    {
        parent::__construct($cache_item_pool);
    }
}
