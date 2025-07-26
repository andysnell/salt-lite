<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache;

use PhoneBurner\SaltLite\Cache\Psr6\NullCachePool;

final class NullCache extends CacheAdapter
{
    public function __construct()
    {
        parent::__construct(new NullCachePool());
    }
}
