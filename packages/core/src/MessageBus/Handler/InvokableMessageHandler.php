<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\MessageBus\Handler;

use PhoneBurner\SaltLite\Container\InvokingContainer;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingComplete;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingFailed;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingStarting;
use Psr\EventDispatcher\EventDispatcherInterface;

class InvokableMessageHandler
{
    public function __construct(
        private readonly InvokingContainer $container,
        private readonly EventDispatcherInterface $event_dispatcher,
    ) {
    }

    public function __invoke(object $message): void
    {
        try {
            $this->event_dispatcher->dispatch(new InvokableMessageHandlingStarting($message));
            $this->container->call($message);
            $this->event_dispatcher->dispatch(new InvokableMessageHandlingComplete($message));
        } catch (\Throwable $e) {
            $this->event_dispatcher->dispatch(new InvokableMessageHandlingFailed($message, $e));
            throw $e;
        }
    }
}
