<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Logging;

use PhoneBurner\SaltLite\Logging\LogEntry;
use PhoneBurner\SaltLite\Logging\Loggable;
use PhoneBurner\SaltLite\Logging\LogLevel;
use PhoneBurner\SaltLite\Logging\PsrLoggerAdapter;
use PhoneBurner\SaltLite\Tests\Fixtures\MockLogger;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PsrLoggerAdapterTest extends TestCase
{
    private MockLogger $test_logger;
    private PsrLoggerAdapter $adapter;

    protected function setUp(): void
    {
        $this->test_logger = new MockLogger();
        $this->adapter = new PsrLoggerAdapter($this->test_logger);
    }

    #[Test]
    public function itDelegatesLogCallsToInnerLogger(): void
    {
        $this->adapter->log('info', 'Test message 1', ['key' => 'value']);
        $this->adapter->log('error', 'Test message 2');
        $this->adapter->log('debug', 'Test message 3', ['data' => ['nested' => true]]);

        $logs = $this->test_logger->getLogs();
        self::assertCount(3, $logs);

        self::assertSame('info', $logs[0]['level']);
        self::assertSame('Test message 1', $logs[0]['message']);
        self::assertSame(['key' => 'value'], $logs[0]['context']);

        self::assertSame('error', $logs[1]['level']);
        self::assertSame('Test message 2', $logs[1]['message']);
        self::assertSame([], $logs[1]['context']);

        self::assertSame('debug', $logs[2]['level']);
        self::assertSame('Test message 3', $logs[2]['message']);
        self::assertSame(['data' => ['nested' => true]], $logs[2]['context']);
    }

    #[Test]
    public function itDelegatesLogLevelEnumToInnerLogger(): void
    {
        $this->adapter->log(LogLevel::Warning, 'Test with enum', ['context' => true]);

        $logs = $this->test_logger->getLogs();
        self::assertCount(1, $logs);
        self::assertSame('warning', $logs[0]['level']);
        self::assertSame('Test with enum', $logs[0]['message']);
        self::assertSame(['context' => true], $logs[0]['context']);
    }

    #[Test]
    public function itAcceptsStringableMessage(): void
    {
        $stringable = new class () implements \Stringable {
            public function __toString(): string
            {
                return 'Stringable message';
            }
        };

        $this->adapter->log('notice', $stringable);

        $logs = $this->test_logger->getLogs();
        self::assertCount(1, $logs);
        self::assertSame('notice', $logs[0]['level']);
        self::assertSame($stringable, $logs[0]['message']);
        self::assertSame([], $logs[0]['context']);
    }

    #[Test]
    public function itProcessesLogEntryObjects(): void
    {
        $entry = new LogEntry(LogLevel::Critical, 'Critical error', ['error' => 'details']);

        $this->adapter->add($entry);

        $logs = $this->test_logger->getLogs();
        self::assertCount(1, $logs);
        self::assertSame(LogLevel::Critical, $logs[0]['level']);
        self::assertSame('Critical error', $logs[0]['message']);
        self::assertSame(['error' => 'details'], $logs[0]['context']);
    }

    #[Test]
    public function itProcessesLoggableObjects(): void
    {
        $loggable = new class () implements Loggable {
            public function getLogEntry(): LogEntry
            {
                return new LogEntry(LogLevel::Alert, 'Alert from loggable', ['source' => 'test']);
            }
        };

        $this->adapter->add($loggable);

        $logs = $this->test_logger->getLogs();
        self::assertCount(1, $logs);
        self::assertSame(LogLevel::Alert, $logs[0]['level']);
        self::assertSame('Alert from loggable', $logs[0]['message']);
        self::assertSame(['source' => 'test'], $logs[0]['context']);
    }
}
