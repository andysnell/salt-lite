<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App\Event;

use PhoneBurner\SaltLite\App\Kernel;

final readonly class KernelExecutionStart
{
    public function __construct(public Kernel $kernel)
    {
    }
}
