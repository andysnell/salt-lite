<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Middleware;

use PhoneBurner\SaltLite\Http\Middleware\Exception\InvalidMiddlewareConfiguration;
use PhoneBurner\SaltLite\Type\Type;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Given an array of MiddlewareInterface instances, or MiddlewareInterface class
 * name strings, this class will produce RequestHandler structures resolved from
 * the PSR-11 container. If the container happens to be a `\PhoneBurner\SaltLite\Framework\Container\LazyContainer`, then
 * the middleware will be resolved lazily.
 */
final readonly class LazyMiddlewareRequestHandlerFactory implements MiddlewareRequestHandlerFactory
{
    /**
     * @param \Closure(MiddlewareInterface): MiddlewareInterface $proxy_factory
     */
    private \Closure $proxy_factory;

    /**
     * We only need a single instance of the proxy factory, so we can define it
     * here and and reference it later, instead of creating a new one for each middleware.
     * Note that since the Lazy Proxy factory cannot return a lazy object,
     * we need to call the `initializeLazyObject` method on the reflector of
     * the object to return the initialized object. If the object is not lazy,
     * this is a no-op.
 */
    public function __construct(
        private ContainerInterface $container,
        private EventDispatcherInterface $event_dispatcher,
    ) {
        $this->proxy_factory = static function (MiddlewareInterface $object) use ($container): MiddlewareInterface {
            return new \ReflectionClass($object)->initializeLazyObject(
                Type::of(MiddlewareInterface::class, $container->get($object::class)),
            );
        };
    }

    /**
     * @param iterable<MiddlewareInterface|class-string<MiddlewareInterface>> $middleware_chain
     */
    #[\Override]
    public function queue(
        RequestHandlerInterface $fallback_handler,
        iterable $middleware_chain = [],
    ): MiddlewareQueue {
        $middleware_handler = MiddlewareQueue::make($fallback_handler, $this->event_dispatcher);
        foreach ($middleware_chain as $middleware) {
            $this->resolve($middleware_handler, $middleware);
        }

        return $middleware_handler;
    }

    /**
     * @param iterable<MiddlewareInterface|class-string<MiddlewareInterface>> $middleware_chain
     */
    #[\Override]
    public function stack(
        RequestHandlerInterface $fallback_handler,
        iterable $middleware_chain = [],
    ): MiddlewareStack {
        $middleware_handler = MiddlewareStack::make($fallback_handler, $this->event_dispatcher);
        foreach ($middleware_chain as $middleware) {
            $this->resolve($middleware_handler, $middleware);
        }

        return $middleware_handler;
    }

    private function resolve(
        MutableMiddlewareRequestHandler $handler,
        MiddlewareInterface|string $middleware,
    ): MutableMiddlewareRequestHandler {
        return match (true) {
            $middleware instanceof MiddlewareInterface => $handler->push($middleware),
            \is_a($middleware, MiddlewareInterface::class, true) => $this->pushMiddlewareClass($handler, $middleware),
            default => throw new InvalidMiddlewareConfiguration(ErrorMessage::RESOLUTION_ERROR),
        };
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware_class
     */
    private function pushMiddlewareClass(
        MutableMiddlewareRequestHandler $handler,
        string $middleware_class,
    ): MutableMiddlewareRequestHandler {
        $reflection = new \ReflectionClass($middleware_class);
        return $handler->push(match ($reflection->isInstantiable()) {
            true => $reflection->newLazyProxy($this->proxy_factory),
            false => LazyMiddleware::make($this->container, $middleware_class),
        });
    }
}
