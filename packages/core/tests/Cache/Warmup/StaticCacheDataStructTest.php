<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cache\Warmup;

use PhoneBurner\SaltLite\Cache\CacheKey;
use PhoneBurner\SaltLite\Cache\Warmup\StaticCacheDataStruct;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StaticCacheDataStructTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $key = new CacheKey('foo');
        $value = new \stdClass();

        $dataStruct = new StaticCacheDataStruct($key, $value);

        self::assertSame($key, $dataStruct->key);
        self::assertSame($value, $dataStruct->value);
    }
}
