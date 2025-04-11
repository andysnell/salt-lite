<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory\MethodServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\ServiceFactoryTestClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class MethodServiceFactoryTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    #[Test]
    public function invokesDefaultMakeMethod(): void
    {
        $factory = new MethodServiceFactory(ServiceFactoryTestClass::class);
        $service = new ServiceFactoryTestClass('from make');
        $this->container->method('get')->with(ServiceFactoryTestClass::class)->willReturn($service);

        $result = $factory($this->container, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('from make', $result->getValue());
    }

    #[Test]
    public function invokesSpecifiedMethod(): void
    {
        $factory = new MethodServiceFactory(ServiceFactoryTestClass::class, 'create');
        $service = new ServiceFactoryTestClass('from create');
        $this->container->method('get')->with(ServiceFactoryTestClass::class)->willReturn($service);

        $result = $factory($this->container, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('from create', $result->getValue());
    }

    #[Test]
    public function throwsWhenServiceNotFound(): void
    {
        $factory = new MethodServiceFactory(ServiceFactoryTestClass::class);
        $this->container->method('get')->with(ServiceFactoryTestClass::class)->willReturn(null);

        $this->expectException(\TypeError::class);
        $this->expectExceptionMessage('method_exists(): Argument #1 ($object_or_class) must be of type object|string, null given');
        $factory($this->container, ServiceFactoryTestClass::class);
    }
}
