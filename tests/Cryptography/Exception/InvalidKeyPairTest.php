<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Exception;

use PhoneBurner\SaltLite\Cryptography\Exception\InvalidKeyPair;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidKeyPairTest extends TestCase
{
    #[Test]
    public function happyPathTestLength(): void
    {
        self::assertSame('Key Pair Must Be Exactly 16 Bytes', InvalidKeyPair::length(16)->getMessage());
    }
}
