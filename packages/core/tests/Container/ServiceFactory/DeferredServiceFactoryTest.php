<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory\DeferredServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\MockServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\ServiceFactoryTestClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DeferredServiceFactoryTest extends TestCase
{
    #[Test]
    public function resolutionOfTheWrappedServiceFactoryIsDeferred(): void
    {
        $service = new ServiceFactoryTestClass();

        $app = $this->createMock(App::class);
        $app->expects($this->once())
            ->method('get')
            ->with(MockServiceFactory::class)
            ->willReturn(new MockServiceFactory($service, ServiceFactoryTestClass::class));

        $sut = new DeferredServiceFactory(MockServiceFactory::class);

        self::assertSame($service, $sut($app, ServiceFactoryTestClass::class));
    }
}
