<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory;
use PhoneBurner\SaltLite\Type\Type;

/**
 * A service factory that defers the creation of the service-factory until the
 * service it provides is requested. This is useful for service factories that
 * are expensive to create or have service dependencies that may not be available
 * at the time the factory is configured to the service.
 */
class DeferredServiceFactory implements ServiceFactory
{
    /**
     * @param class-string<ServiceFactory> $service_factory
     */
    public function __construct(private readonly string $service_factory)
    {
        \assert(Type::isClassStringOf(ServiceFactory::class, $service_factory));
    }

    public function __invoke(App $app, string $id): object
    {
        $callback = $app->get($this->service_factory);
        \assert(\is_callable($callback), \sprintf("The service factory '%s' must be callable.", $this->service_factory));
        return $callback($app, $id);
    }
}
