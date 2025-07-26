<?php

declare(strict_types=1);

namespace App\Tests\Unit\TestSupport;

use Doctrine\DBAL\Connection;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\Container\ServiceContainer;
use PhoneBurner\SaltLite\Framework\App\App;
use PhoneBurner\SaltLite\Framework\Http\HttpKernel;
use PhoneBurner\SaltLite\Framework\MessageBus\Container\MessageBusContainer;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory;
use PhoneBurner\SaltLite\Logging\LogTrace;
use PhoneBurner\SaltLite\Uuid\Uuid;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @phpstan-require-extends TestCase
 */
trait HasApplicationLifecycle
{
    protected ServiceContainer|null $services = null;

    #[Before]
    protected function bootstrapApplication(): void
    {
        $this->services = App::bootstrap(Context::Test)->services;
        $this->services->get(Connection::class)->beginTransaction();
        $this->services->set(LogTrace::class, new LogTrace(Uuid::nil()));
        $this->services->set(EmitterInterface::class, new MockEmitter());
        $this->services->set(EventDispatcherInterface::class, new MockEventDispatcher());
        $this->services->set(
            TransportFactory::class,
            static fn(App $app): TransportFactory => new ForceSyncTransportFactory(
                $app->get(MessageBusContainer::class),
            ),
        );
    }

    #[After]
    protected function teardownApplication(): void
    {
        $this->services?->get(Connection::class)->rollBack();
        $this->services = null;
        App::teardown();
    }

    protected function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->container()->get(HttpKernel::class)->run($request);
        $emitter = $this->container()->get(EmitterInterface::class);

        self::assertInstanceOf(MockEmitter::class, $emitter);
        self::assertInstanceOf(ResponseInterface::class, $emitter->response);

        return $emitter->response;
    }

    protected function container(): ServiceContainer
    {
        return $this->services ?? throw new \RuntimeException('Container not initialized');
    }
}
