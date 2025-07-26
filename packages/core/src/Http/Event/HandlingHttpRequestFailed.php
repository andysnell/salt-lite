<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Event;

use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\Loggable;
use PhoneBurner\SaltLite\Logging\LogLevel;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HandlingHttpRequestFailed implements Loggable
{
    public function __construct(public ServerRequestInterface|null $request, public \Throwable $e)
    {
    }

    public function getLogEntry(): LogEntry
    {
        return new LogEntry(LogLevel::Error, message: 'HTTP Request Handling Failed', context: [
            'method' => $this->request?->getMethod(),
            'uri' => (string)$this->request?->getUri(),
            'exception' => $this->e,
        ]);
    }
}
