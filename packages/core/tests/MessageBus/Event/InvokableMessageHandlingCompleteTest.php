<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\MessageBus\Event;

use PhoneBurner\SaltLite\MessageBus\Event\InvokableMessageHandlingComplete;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class InvokableMessageHandlingCompleteTest extends TestCase
{
    #[Test]
    public function constructorSetsMessageProperty(): void
    {
        $message = new stdClass();
        $event = new InvokableMessageHandlingComplete($message);

        self::assertSame($message, $event->message);
    }
}
