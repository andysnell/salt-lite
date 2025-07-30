<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container;

use PhoneBurner\SaltLite\App\App;

/**
 * Unlike the other "factories" in this namespace, this one is not
 * responsible for creating a single service, but for creating the
 * application-level service container.
 */
interface ServiceContainerFactory
{
    public function make(App $app): ServiceContainer;
}
