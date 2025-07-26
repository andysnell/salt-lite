<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\App\Environment;
use PhoneBurner\SaltLite\Configuration\Configuration;
use PhoneBurner\SaltLite\Configuration\ImmutableConfiguration;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideCollection;
use PhoneBurner\SaltLite\Container\ServiceContainer;

use const PhoneBurner\SaltLite\UNIT_TEST_ROOT;

class MockApp implements App
{
    public function __construct(
        public ServiceContainer $services = new MockContainer(),
        public Context $context = Context::Test,
        public Environment $environment = new MockEnvironment(UNIT_TEST_ROOT),
        public Configuration $config = new ImmutableConfiguration([]),
    ) {
    }

    public function get(\Stringable|string $id): mixed
    {
        return $this->services->get($id);
    }

    public function has(\Stringable|string $id): bool
    {
        return $this->services->has($id);
    }

    public function set(\Stringable|string $id, mixed $value): void
    {
        $this->services->set($id, $value);
    }

    public function unset(\Stringable|string $id): void
    {
        $this->services->unset($id);
    }

    public function call(
        object|string $object,
        string $method = '__invoke',
        OverrideCollection|null $overrides = null,
    ): mixed {
        return $this->services->call($object, $method, $overrides);
    }

    public function __get(string $name): mixed
    {
        if ($name === 'services') {
            return $this->services;
        }
        throw new \RuntimeException(\sprintf('Property %s not found', $name));
    }

    public static function booted(): bool
    {
        return true;
    }

    public static function instance(): App
    {
        throw new \RuntimeException('Not implemented');
    }

    public static function bootstrap(Context $context): App
    {
        throw new \RuntimeException('Not implemented');
    }

    public static function teardown(): null
    {
        return null;
    }

    public static function exec(Context $context, callable $callback): mixed
    {
        throw new \RuntimeException('Not implemented');
    }
}
