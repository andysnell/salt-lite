<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory;

/**
 * Instantiates a new object of the given class, passing the given arguments to
 * the constructor. If the class is not provided in the constructor, we'll use
 * the entry id of the service being resolved by the container.
 */
final readonly class NewInstanceServiceFactory implements ServiceFactory
{
    /**
     * @param class-string|null $class
     * @param array<array-key, mixed> $args
     */
    public function __construct(
        private string|null $class = null,
        private array $args = [],
    ) {
    }

    public function __invoke(App $app, string $id): object
    {
        return new ($this->class ?? $id)(...$this->args);
    }
}
