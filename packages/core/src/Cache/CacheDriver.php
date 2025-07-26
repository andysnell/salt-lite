<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache;

use PhoneBurner\SaltLite\Enum\WithStringBackedInstanceStaticMethod;

enum CacheDriver: string
{
    use WithStringBackedInstanceStaticMethod;

    case File = 'file';
    case Memory = 'memory';
    case None = 'none';
    case Remote = 'remote';
}
