<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing\Definition;

use PhoneBurner\SaltLite\Http\Domain\HttpMethod;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

interface Definition
{
    /**
     * @param callable(static): static ...$callbacks
     */
    public function with(callable ...$callbacks): self;

    public function withRoutePath(string $path): self;

    public function withMethod(HttpMethod ...$method): self;

    public function withAddedMethod(HttpMethod ...$method): self;

    public function withName(string $name): self;

    /**
     * @param class-string<RequestHandlerInterface> $handler_class
     */
    public function withHandler(string $handler_class): self;

    /**
     * @param class-string<MiddlewareInterface> ...$middleware
     */
    public function withMiddleware(string ...$middleware): self;

    public function withAttribute(string $name, mixed $value): self;

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAttributes(array $attributes): self;

    /**
     * @param array<string, mixed> $attributes
     */
    public function withAddedAttributes(array $attributes): self;
}
