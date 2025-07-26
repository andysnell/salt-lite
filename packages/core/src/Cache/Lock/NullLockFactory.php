<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Lock;

use PhoneBurner\SaltLite\Time\Ttl;

final class NullLockFactory implements LockFactory
{
    #[\Override]
    public function make(\Stringable|string $key, Ttl $ttl = new Ttl(300), bool $auto_release = true): NullLock
    {
        return new NullLock($ttl);
    }
}
