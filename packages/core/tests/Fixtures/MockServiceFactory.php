<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory;

final readonly class MockServiceFactory implements ServiceFactory
{
    public function __construct(
        private object $service,
        private string|null $expected_id = null,
    ) {
    }

    public function __invoke(App $app, string $id): object
    {
        if ($this->expected_id !== null && $this->expected_id !== $id) {
            throw new \InvalidArgumentException(
                \sprintf('Expected service ID "%s", but got "%s".', $this->expected_id, $id),
            );
        }

        return $this->service;
    }
}
