<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory;

final readonly class CallableServiceFactory implements ServiceFactory
{
    public function __construct(private \Closure $closure)
    {
    }

    public function __invoke(App $app, string $id): object
    {
        return ($this->closure)($app);
    }
}
