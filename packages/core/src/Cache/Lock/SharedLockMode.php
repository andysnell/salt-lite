<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Lock;

enum SharedLockMode
{
    case Write;
    case Read;
}
