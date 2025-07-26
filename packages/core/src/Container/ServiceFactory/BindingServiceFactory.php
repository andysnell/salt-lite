<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory;
use Psr\Container\ContainerInterface;

/**
 * Factory class for binding an id (e.g. interface) to an entry in the container
 *(entry_id).
 */
final readonly class BindingServiceFactory implements ServiceFactory
{
    public function __construct(public string $entry_id)
    {
    }

    public function __invoke(ContainerInterface $app, string $id): object
    {
        $entry = $app->get($this->entry_id);
        \assert(\is_object($entry), \sprintf("The entry with id '%s' must be an object.", $this->entry_id));
        return $entry;
    }
}
