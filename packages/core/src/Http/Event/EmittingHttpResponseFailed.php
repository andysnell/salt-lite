<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Event;

use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\Loggable;
use PhoneBurner\SaltLite\Logging\LogLevel;
use Psr\Http\Message\ResponseInterface;

final readonly class EmittingHttpResponseFailed implements Loggable
{
    public function __construct(public ResponseInterface $response, public \Throwable $e)
    {
    }

    public function getLogEntry(): LogEntry
    {
        return new LogEntry(LogLevel::Critical, message: 'An unhandled error occurred while emitting the request', context: [
            'exception' => $this->e,
        ]);
    }
}
