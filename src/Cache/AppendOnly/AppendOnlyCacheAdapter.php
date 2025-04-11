<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\AppendOnly;

use PhoneBurner\SaltLite\Cache\AppendOnlyCache;
use PhoneBurner\SaltLite\Cache\CacheKey;
use PhoneBurner\SaltLite\Cache\Exception\CacheWriteFailed;
use PhoneBurner\SaltLite\Cache\Psr6\InMemoryCachePool;
use PhoneBurner\SaltLite\Time\Ttl;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;

/**
 * Adapts a PSR-16 cache instance to our "Append Only" Cache interface
 *
 * @link https://www.php-fig.org/psr/psr-16/
 */
class AppendOnlyCacheAdapter implements AppendOnlyCache, CacheInterface, CacheItemPoolInterface
{
    public function __construct(
        private readonly CacheItemPoolInterface $pool = new InMemoryCachePool(),
    ) {
    }

    public function get(\Stringable|string $key, mixed $default = null): mixed
    {
        $item = $this->pool->getItem(self::normalize($key));
        return $item->isHit() ? $item->get() : $default;
    }

    /**
     * @param iterable<string|\Stringable> $keys
     */
    public function getMultiple(iterable $keys, mixed $default = null): iterable
    {
        $items = [];
        foreach ($this->getItems($keys) as $item) {
            $items[$item->getKey()] = $item->isHit() ? $item->get() : $default;
        }

        return $items;
    }

    public function set(\Stringable|string $key, mixed $value, Ttl|\DateInterval|int|null $ttl = null): bool
    {
        $item = $this->getItem($key)->set($value)->expiresAfter(null);
        return $this->save($item);
    }

    public function setMultiple(iterable $values, Ttl|\DateInterval|int|null $ttl = null): bool
    {
        foreach ($values as $key => $value) {
            $item = $this->pool->getItem(self::normalize($key))->set($value)->expiresAfter(null);
            $this->pool->saveDeferred($item);
        }

        return $this->pool->commit();
    }

    public function delete(\Stringable|string $key): bool
    {
        throw new CacheWriteFailed('AppendOnlyCache does not support delete operations');
    }

    public function deleteMultiple(iterable $keys): bool
    {
        throw new CacheWriteFailed('AppendOnlyCache does not support delete operations');
    }

    public function remember(
        \Stringable|string $key,
        callable $callback,
    ): mixed {
        $key = self::normalize($key);
        $value = $this->get($key);
        if ($value !== null) {
            return $value;
        }

        $value = $callback();
        if ($value !== null) {
            $this->set($key, $value, null) || throw new CacheWriteFailed('set: ' . $key);
        }

        return $value;
    }

    public function forget(\Stringable|string $key): mixed
    {
        throw new CacheWriteFailed('AppendOnlyCache does not support delete operations');
    }

    public function clear(): bool
    {
        return $this->pool->clear();
    }

    public function has(string $key): bool
    {
        return $this->pool->hasItem($key);
    }

    private static function normalize(\Stringable|string $key): string
    {
        return $key instanceof CacheKey ? $key->normalized : CacheKey::make($key)->normalized;
    }

    /**
     * @param iterable<string|\Stringable> $keys
     * @return array<string>
     */
    private static function keys(iterable $keys): array
    {
        $normalized = [];
        foreach ($keys as $key) {
            $normalized[] = self::normalize($key);
        }
        return $normalized;
    }

    public function getItem(\Stringable|string $key): CacheItemInterface
    {
        return $this->pool->getItem(self::normalize($key));
    }

    /**
     * @param iterable<string|\Stringable> $keys
     * @return iterable<CacheItemInterface>
     */
    public function getItems(iterable $keys = []): iterable
    {
        return $this->pool->getItems(self::keys($keys));
    }

    public function hasItem(\Stringable|string $key): bool
    {
        return $this->pool->hasItem(self::normalize($key));
    }

    public function deleteItem(\Stringable|string $key): bool
    {
        throw new CacheWriteFailed('AppendOnlyCache does not support delete operations');
    }

    /**
     * @param iterable<string|\Stringable> $keys
     */
    public function deleteItems(iterable $keys): bool
    {
        throw new CacheWriteFailed('AppendOnlyCache does not support delete operations');
    }

    public function save(CacheItemInterface $item): bool
    {
        return $this->pool->save($item);
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->pool->saveDeferred($item);
    }

    public function commit(): bool
    {
        return $this->pool->commit();
    }
}
