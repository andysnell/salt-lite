<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\MessageBus\Handler;

use PhoneBurner\SaltLite\Container\InvokingContainer;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingComplete;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingFailed;
use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingStarting;
use PhoneBurner\SaltLite\MessageBus\Handler\InvokableMessageHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;
use RuntimeException;
use stdClass;

final class InvokableMessageHandlerTest extends TestCase
{
    #[Test]
    public function invokeDispatchesEventsAndCallsContainer(): void
    {
        $message = new stdClass();
        $container = $this->createMock(InvokingContainer::class);
        $event_dispatcher = $this->createMock(EventDispatcherInterface::class);
        $handler = new InvokableMessageHandler($container, $event_dispatcher);

        $dispatched_events = [];
        $event_dispatcher->method('dispatch')->willReturnCallback(static function (object $event) use (&$dispatched_events): object {
                $dispatched_events[] = $event;
                return $event;
        });

        ($handler)($message);

        self::assertCount(2, $dispatched_events);
        self::assertInstanceOf(InvokableMessageHandlingStarting::class, $dispatched_events[0]);
        self::assertSame($message, $dispatched_events[0]->message);
        self::assertInstanceOf(InvokableMessageHandlingComplete::class, $dispatched_events[1]);
        self::assertSame($message, $dispatched_events[1]->message);
    }

    #[Test]
    public function invokeDispatchesFailedEventOnException(): void
    {
        $message = new stdClass();
        $exception = new RuntimeException('Test exception');

        $container = $this->createMock(InvokingContainer::class);
        $container->method('call')
            ->willThrowException($exception);

        $event_dispatcher = $this->createMock(EventDispatcherInterface::class);
        $handler = new InvokableMessageHandler($container, $event_dispatcher);

        $dispatched_events = [];
        $event_dispatcher->method('dispatch')
            ->willReturnCallback(function (object $event) use (&$dispatched_events): object {
                $dispatched_events[] = $event;
                return $event;
            });

        // Act and Assert
        try {
            ($handler)($message);
            self::fail('Exception was not thrown');
        } catch (RuntimeException $e) {
            self::assertSame($exception, $e);
            self::assertCount(2, $dispatched_events);
            self::assertInstanceOf(InvokableMessageHandlingStarting::class, $dispatched_events[0]);
            self::assertSame($message, $dispatched_events[0]->message);
            self::assertInstanceOf(InvokableMessageHandlingFailed::class, $dispatched_events[1]);
            self::assertSame($message, $dispatched_events[1]->message);
            self::assertSame($exception, $dispatched_events[1]->exception);
        }
    }
}
