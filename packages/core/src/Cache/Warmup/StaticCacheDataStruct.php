<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Warmup;

use PhoneBurner\SaltLite\Cache\CacheKey;

readonly class StaticCacheDataStruct
{
    public function __construct(
        public CacheKey $key,
        public mixed $value,
    ) {
    }
}
