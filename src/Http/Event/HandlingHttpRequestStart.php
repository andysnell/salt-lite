<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Event;

use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\Loggable;
use Psr\Http\Message\ServerRequestInterface;

final readonly class HandlingHttpRequestStart implements Loggable
{
    public function __construct(public ServerRequestInterface $request)
    {
    }

    public function getLogEntry(): LogEntry
    {
        return new LogEntry(message: 'HTTP Request Received', context: [
            'method' => $this->request->getMethod(),
            'uri' => (string)$this->request->getUri(),
        ]);
    }
}
