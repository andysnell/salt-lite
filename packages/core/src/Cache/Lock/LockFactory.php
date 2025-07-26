<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Lock;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Time\Ttl;

#[Contract]
interface LockFactory
{
    public function make(\Stringable|string $key, Ttl $ttl = new Ttl(300), bool $auto_release = true): Lock;
}
