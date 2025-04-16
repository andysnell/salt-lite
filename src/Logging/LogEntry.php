<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Logging;

use PhoneBurner\SaltLite\Logging\LogLevel;

/**
 * DTO representing a PSR-3 log event without a time component.
 */
readonly class LogEntry
{
    /**
     * @param array<array-key, mixed> $context
     */
    public function __construct(
        public LogLevel $level = LogLevel::Debug,
        public \Stringable|string $message = '',
        public array $context = [],
    ) {
    }
}
