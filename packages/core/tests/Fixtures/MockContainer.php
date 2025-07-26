<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideCollection;
use PhoneBurner\SaltLite\Container\ServiceContainer;
use PhoneBurner\SaltLite\Exception\NotImplemented;
use Psr\Log\LoggerInterface;

/**
 * Simple container for testing
 */
class MockContainer implements ServiceContainer
{
    private array $services = [];
    private array $requested_services = [];

    public function get(\Stringable|string $id): mixed
    {
        $id = (string)$id;
        $this->requested_services[$id] = true;

        if (! isset($this->services[$id])) {
            throw new \RuntimeException('Service not found: ' . $id);
        }

        return $this->services[$id];
    }

    public function has(\Stringable|string $id, bool $strict = false): bool
    {
        return isset($this->services[(string)$id]);
    }

    public function registerService(string $id, mixed $service): void
    {
        $this->services[$id] = $service;
    }

    public function wasServiceRequested(string $id): bool
    {
        return isset($this->requested_services[$id]);
    }

    public function call(
        object|string $object,
        string $method = '__invoke',
        OverrideCollection|null $overrides = null,
    ): mixed {
        throw new NotImplemented(__METHOD__);
    }

    public function setLogger(LoggerInterface $logger): void
    {
        throw new NotImplemented(__METHOD__);
    }

    public function unset(\Stringable|string $id): void
    {
        throw new NotImplemented(__METHOD__);
    }

    public function set(\Stringable|string $id, mixed $value): void
    {
        throw new NotImplemented(__METHOD__);
    }
}
