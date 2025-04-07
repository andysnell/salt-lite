<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Paseto;

use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\PasetoMessage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PasetoMessageTest extends TestCase
{
    #[Test]
    public function null_case(): void
    {
        $message = PasetoMessage::make([], []);

        self::assertEquals(new PasetoMessage(''), $message);
        self::assertSame('', $message->payload);
        self::assertSame('', $message->footer);
        self::assertSame([], $message->payload()->claims);
        self::assertSame([], $message->footer()->claims);
    }

    #[Test]
    public function simple_case(): void
    {
        $message = PasetoMessage::make([
            'foo' => 42,
        ]);

        self::assertEquals(new PasetoMessage('{"foo":42}'), $message);
        self::assertSame('{"foo":42}', $message->payload);
        self::assertSame('', $message->footer);
        self::assertSame(['foo' => 42], $message->payload()->claims);
        self::assertSame([], $message->footer()->claims);
    }

    #[Test]
    public function with_footer_and_additional_data(): void
    {
        $message = PasetoMessage::make(
            ['foo' => 42],
            ['bar' => 'baz'],
        );

        self::assertEquals(new PasetoMessage('{"foo":42}', '{"bar":"baz"}'), $message);
        self::assertSame('{"foo":42}', $message->payload);
        self::assertSame('{"bar":"baz"}', $message->footer);
        self::assertSame(['foo' => 42], $message->payload()->claims);
        self::assertSame(['bar' => 'baz'], $message->footer()->claims);
    }
}
