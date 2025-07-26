<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem\Exception;

use PhoneBurner\SaltLite\Filesystem\Exception\UnableToCreateDirectory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnableToCreateDirectory::class)]
final class UnableToCreateDirectoryTest extends TestCase
{
    #[Test]
    public function atLocationWithOnlyLocation(): void
    {
        $location = '/path/to/dir';
        $exception = UnableToCreateDirectory::atLocation($location);

        self::assertInstanceOf(UnableToCreateDirectory::class, $exception);
        self::assertInstanceOf(\RuntimeException::class, $exception);
        self::assertSame(
            'Unable to create directory at location: /path/to/dir.',
            $exception->getMessage(),
        );
        self::assertNull($exception->getPrevious());
    }

    #[Test]
    public function atLocationWithLocationAndReason(): void
    {
        $location = '/another/path';
        $reason = 'Permission denied';
        $exception = UnableToCreateDirectory::atLocation($location, $reason);

        self::assertInstanceOf(UnableToCreateDirectory::class, $exception);
        self::assertSame(
            'Unable to create directory at location: /another/path. Permission denied',
            $exception->getMessage(),
        );
        self::assertNull($exception->getPrevious());
    }

    #[Test]
    public function atLocationWithAllArguments(): void
    {
        $location = '/tmp/test';
        $reason = 'Filesystem error';
        $previous = new \Exception('Previous error');
        $exception = UnableToCreateDirectory::atLocation($location, $reason, $previous);

        self::assertInstanceOf(UnableToCreateDirectory::class, $exception);
        self::assertSame(
            'Unable to create directory at location: /tmp/test. Filesystem error',
            $exception->getMessage(),
        );
        self::assertSame($previous, $exception->getPrevious());
    }
}
