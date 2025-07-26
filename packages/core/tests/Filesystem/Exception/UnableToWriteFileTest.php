<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem\Exception;

use PhoneBurner\SaltLite\Filesystem\Exception\UnableToWriteFile;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnableToWriteFile::class)]
final class UnableToWriteFileTest extends TestCase
{
    #[Test]
    public function atLocationWithOnlyLocation(): void
    {
        $location = '/path/to/file.txt';
        $exception = UnableToWriteFile::atLocation($location);

        self::assertInstanceOf(UnableToWriteFile::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertSame(
            'Unable to write file at location: /path/to/file.txt.',
            $exception->getMessage(),
        );
        self::assertNull($exception->getPrevious());
    }

    #[Test]
    public function atLocationWithLocationAndReason(): void
    {
        $location = '/another/file.log';
        $reason = 'Disk full';
        $exception = UnableToWriteFile::atLocation($location, $reason);

        self::assertInstanceOf(UnableToWriteFile::class, $exception);
        self::assertSame(
            'Unable to write file at location: /another/file.log. Disk full',
            $exception->getMessage(),
        );
        self::assertNull($exception->getPrevious());
    }

    #[Test]
    public function atLocationWithAllArguments(): void
    {
        $location = '/tmp/data.json';
        $reason = 'I/O error';
        $previous = new \Exception('Underlying filesystem error');
        $exception = UnableToWriteFile::atLocation($location, $reason, $previous);

        self::assertInstanceOf(UnableToWriteFile::class, $exception);
        self::assertSame(
            'Unable to write file at location: /tmp/data.json. I/O error',
            $exception->getMessage(),
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
