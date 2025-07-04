<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Middleware;

use PhoneBurner\SaltLite\Http\Middleware\Exception\InvalidMiddlewareConfiguration;
use PhoneBurner\SaltLite\Type\Type;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddleware implements TerminableMiddleware
{
    private RequestHandlerInterface|null $fallback_request_handler = null;

    /**
     * @param class-string<MiddlewareInterface> $middleware
     */
    public function __construct(private readonly ContainerInterface $container, public readonly string $middleware)
    {
        if (! Type::isClassStringOf(MiddlewareInterface::class, $middleware)) {
            throw new InvalidMiddlewareConfiguration(\sprintf(ErrorMessage::INVALID_CLASS, $middleware));
        }
    }

    /**
     * @param class-string<MiddlewareInterface> $middleware
     */
    public static function make(ContainerInterface $container, string $middleware): self
    {
        return new self($container, $middleware);
    }

    #[\Override]
    public function setFallbackRequestHandler(RequestHandlerInterface $handler): void
    {
        $this->fallback_request_handler = $handler;
    }

    #[\Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $next_middleware = $this->container->get($this->middleware);
        if ($next_middleware instanceof TerminableMiddleware && $this->fallback_request_handler instanceof RequestHandlerInterface) {
            $next_middleware->setFallbackRequestHandler($this->fallback_request_handler);
        }

        \assert($next_middleware instanceof MiddlewareInterface);

        return $next_middleware->process($request, $handler);
    }
}
