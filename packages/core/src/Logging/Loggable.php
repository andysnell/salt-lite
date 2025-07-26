<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Logging;

interface Loggable
{
    public function getLogEntry(): LogEntry;
}
