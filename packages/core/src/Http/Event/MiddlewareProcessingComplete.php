<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Event;

use PhoneBurner\SaltLite\Http\Middleware\LazyMiddleware;
use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\Loggable;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

final readonly class MiddlewareProcessingComplete implements Loggable
{
    public function __construct(
        public MiddlewareInterface $middleware,
        public ServerRequestInterface $request,
        public ResponseInterface $response,
    ) {
    }

    public function getLogEntry(): LogEntry
    {
        return new LogEntry(
            message: 'Processed Request with Middleware: {middleware}',
            context: [
                'middleware' => $this->middleware instanceof LazyMiddleware ? $this->middleware->middleware : $this->middleware::class,
            ],
        );
    }
}
