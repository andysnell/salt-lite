<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cache\Lock;

use PhoneBurner\SaltLite\Cache\Lock\NullLock;
use PhoneBurner\SaltLite\Cache\Lock\NullLockFactory;
use PhoneBurner\SaltLite\Time\Ttl;
use PhoneBurner\SaltLite\Time\TtlRemaining;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NullLockFactoryTest extends TestCase
{
    #[Test]
    public function itReturnsANullLock(): void
    {
        $sut = new NullLockFactory();
        $lock = $sut->make('foo', Ttl::seconds(34), false);
        self::assertInstanceOf(NullLock::class, $lock);
        self::assertEquals(TtlRemaining::seconds(34), $lock->ttl());
    }
}
