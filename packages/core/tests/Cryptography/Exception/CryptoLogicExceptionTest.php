<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Exception;

use PhoneBurner\SaltLite\Cryptography\Exception\CryptoException;
use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(CryptoLogicException::class)]
final class CryptoLogicExceptionTest extends TestCase
{
    #[Test]
    public function unreachableCreatesExceptionWithCorrectMessage(): void
    {
        $exception = CryptoLogicException::unreachable();

        self::assertInstanceOf(CryptoLogicException::class, $exception);
        self::assertInstanceOf(CryptoException::class, $exception);
        self::assertInstanceOf(\LogicException::class, $exception);
        self::assertSame(
            'A code path was executed that would not normally be possible under normal operation.',
            $exception->getMessage(),
        );
    }
}
