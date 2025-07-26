<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceContainer;

use PhoneBurner\SaltLite\Container\Exception\UnableToAutoResolveParameter;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterName;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterPosition;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterType;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideCollection;
use PhoneBurner\SaltLite\Container\ServiceContainer;
use PhoneBurner\SaltLite\Container\ServiceContainer\ReflectionMethodAutoResolver;
use PhoneBurner\SaltLite\Tests\Fixtures\MethodFixture;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ReflectionMethodAutoResolverTest extends TestCase
{
    private ReflectionMethodAutoResolver $resolver;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ServiceContainer::class);
        $this->resolver = new ReflectionMethodAutoResolver($this->container);
    }

    #[Test]
    public function resolvesParameterByPosition(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithParameters');
        $parameter = $method->getParameters()[0]; // First parameter: $first

        $override_value = 'position override';
        $override = new OverrideByParameterPosition(0, $override_value);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($override_value, $resolver($parameter));
    }

    #[Test]
    public function resolvesParameterByName(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithParameters');
        $parameter = $method->getParameters()[1]; // Second parameter: $second

        $override_value = 'name override';
        $override = new OverrideByParameterName('second', $override_value);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($override_value, $resolver($parameter));
    }

    #[Test]
    public function resolvesParameterByType(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithTypeHint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();
        $override = new OverrideByParameterType(LoggerInterface::class, $logger);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($logger, $resolver($parameter));
    }

    #[Test]
    public function resolvesParameterFromContainer(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithTypeHint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver($parameter));
    }

    #[Test]
    public function usesDefaultValueWhenNoTypeAndDefaultAvailable(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithDefaultValue');
        $parameter = $method->getParameters()[0]; // Parameter: $param = 'default'

        self::assertSame('default', ($this->resolver)($parameter));
    }

    #[Test]
    public function throwsWhenNoTypeAndNoDefaultAvailable(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithParameters');
        $parameter = $method->getParameters()[0]; // Parameter: $first

        $this->expectException(UnableToAutoResolveParameter::class);
        ($this->resolver)($parameter);
    }

    #[Test]
    public function prefersDefaultValueOverAutowiring(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithDefaultAndType');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger = null

        // Container should not be called
        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(false);
        $container->expects($this->never())
            ->method('get');

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertNull($resolver($parameter));
    }

    #[Test]
    public function fallsBackToContainerResolveWhenNoOtherOptions(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithTypeHint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver($parameter));
    }

    #[Test]
    public function handlesNonNamedType(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithUnionType');
        $parameter = $method->getParameters()[0]; // Parameter: string|int $param

        $this->expectException(UnableToAutoResolveParameter::class);
        ($this->resolver)($parameter);
    }

    #[Test]
    public function handlesBuiltinType(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithBuiltinType');
        $parameter = $method->getParameters()[0]; // Parameter: string $param

        $this->expectException(UnableToAutoResolveParameter::class);
        ($this->resolver)($parameter);
    }

    #[Test]
    public function handlesSelfType(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithSelfType');
        $parameter = $method->getParameters()[0]; // Parameter: self $param

        $this->expectException(UnableToAutoResolveParameter::class);
        ($this->resolver)($parameter);
    }

    #[Test]
    public function supportsNonServiceContainer(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'methodWithTypeHint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class)
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver($parameter));
    }
}
