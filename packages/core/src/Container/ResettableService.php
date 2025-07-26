<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container;

interface ResettableService
{
    /**
     * Reset the service to its initial state. This may have side effects such
     * as flushing buffers, clearing caches, or resetting properties.
     */
    public function reset(): void;
}
