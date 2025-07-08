<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Lock;

use PhoneBurner\SaltLite\Time\TimeInterval\TimeInterval;

final class NullLockFactory implements LockFactory
{
    #[\Override]
    public function make(
        \Stringable|string $key,
        TimeInterval $ttl = new TimeInterval(seconds: 300),
        bool $auto_release = true,
    ): NullLock {
        return new NullLock($ttl);
    }
}
