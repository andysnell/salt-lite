<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory\StaticMethodServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\StaticServiceFactoryTestClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class StaticMethodServiceFactoryTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    #[Test]
    public function invokesDefaultMakeMethodOnClass(): void
    {
        $factory = new StaticMethodServiceFactory(StaticServiceFactoryTestClass::class);
        $result = $factory($this->container, StaticServiceFactoryTestClass::class);

        self::assertInstanceOf(StaticServiceFactoryTestClass::class, $result);
        self::assertSame('from make', $result->getValue());
    }

    #[Test]
    public function invokesSpecifiedMethodOnClass(): void
    {
        $factory = new StaticMethodServiceFactory(StaticServiceFactoryTestClass::class, 'create');
        $result = $factory($this->container, StaticServiceFactoryTestClass::class);

        self::assertInstanceOf(StaticServiceFactoryTestClass::class, $result);
        self::assertSame('from create', $result->getValue());
    }

    #[Test]
    public function invokesDefaultMakeMethodOnObject(): void
    {
        $factory = new StaticMethodServiceFactory(new StaticServiceFactoryTestClass());
        $result = $factory($this->container, StaticServiceFactoryTestClass::class);

        self::assertInstanceOf(StaticServiceFactoryTestClass::class, $result);
        self::assertSame('from make', $result->getValue());
    }

    #[Test]
    public function invokesSpecifiedMethodOnObject(): void
    {
        $factory = new StaticMethodServiceFactory(new StaticServiceFactoryTestClass(), 'create');
        $result = $factory($this->container, StaticServiceFactoryTestClass::class);

        self::assertInstanceOf(StaticServiceFactoryTestClass::class, $result);
        self::assertSame('from create', $result->getValue());
    }
}
