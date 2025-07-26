<?php

declare(strict_types=1);

namespace App\Tests\Unit\TestSupport;

use PhoneBurner\SaltLite\Cache\Lock\Lock;
use PhoneBurner\SaltLite\Cache\Lock\SharedLockMode;
use PhoneBurner\SaltLite\Time\Ttl;
use PhoneBurner\SaltLite\Time\TtlRemaining;

class SpyLock implements Lock
{
    public function __construct(
        public Ttl|TtlRemaining|null $ttl = null,
        public bool $acquire = true,
        public bool $acquired = true,
        public bool $released = false,
        public bool $refreshed = false,
    ) {
    }

    #[\Override]
    public function acquire(
        bool $blocking = false,
        int $timeout_seconds = 30,
        int $delay_microseconds = 25000,
        SharedLockMode $mode = SharedLockMode::Write,
    ): bool {
        return $this->acquire;
    }

    #[\Override]
    public function release(): void
    {
        $this->released = true;
    }

    #[\Override]
    public function refresh(Ttl|null $ttl = null): void
    {
        $this->refreshed = true;
    }

    #[\Override]
    public function acquired(): bool
    {
        return $this->acquired;
    }

    #[\Override]
    public function ttl(): TtlRemaining|null
    {
        if ($this->ttl instanceof Ttl) {
            return new TtlRemaining($this->ttl->seconds);
        }
        return $this->ttl;
    }
}
