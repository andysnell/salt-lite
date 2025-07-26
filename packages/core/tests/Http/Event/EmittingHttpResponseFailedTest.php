<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Event;

use Laminas\Diactoros\Response;
use PhoneBurner\SaltLite\Http\Event\EmittingHttpResponseFailed;
use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\LogLevel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmittingHttpResponseFailedTest extends TestCase
{
    #[Test]
    public function constructorSetsPublicProperties(): void
    {
        $response = new Response();
        $exception = new \Exception('Test exception');

        $event = new EmittingHttpResponseFailed($response, $exception);

        self::assertSame($response, $event->response);
        self::assertSame($exception, $event->e);
    }

    #[Test]
    public function getLogEntryReturnsLogEntryWithException(): void
    {
        $response = new Response();
        $exception = new \Exception('Test exception');

        $event = new EmittingHttpResponseFailed($response, $exception);
        $log_entry = $event->getLogEntry();

        self::assertInstanceOf(LogEntry::class, $log_entry);
        self::assertSame(LogLevel::Critical, $log_entry->level);
        self::assertSame('An unhandled error occurred while emitting the request', $log_entry->message);
        self::assertArrayHasKey('exception', $log_entry->context);
        self::assertSame($exception, $log_entry->context['exception']);
    }
}
