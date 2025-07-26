<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cache\Lock;

use PhoneBurner\SaltLite\Cache\Lock\NullLock;
use PhoneBurner\SaltLite\Time\Ttl;
use PhoneBurner\SaltLite\Time\TtlRemaining;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NullLockTest extends TestCase
{
    #[Test]
    public function defaultValuesAreSaneForANullLock(): void
    {
        $sut = new NullLock();
        self::assertTrue($sut->acquire());
        self::assertTrue($sut->acquired());
        self::assertNull($sut->ttl());
    }

    #[Test]
    public function valuesCanBeConfigured(): void
    {
        $sut = new NullLock(
            Ttl::seconds(34),
            false,
            false,
        );

        self::assertFalse($sut->acquire());
        self::assertFalse($sut->acquired());
        self::assertEquals(TtlRemaining::seconds(34), $sut->ttl());
    }
}
