<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\MessageBus\Event;

class InvokableMessageHandlingComplete
{
    public function __construct(public readonly object $message)
    {
    }
}
