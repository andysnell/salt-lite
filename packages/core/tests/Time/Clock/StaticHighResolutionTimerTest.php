<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\Clock;

use PhoneBurner\SaltLite\Time\Clock\StaticHighResolutionTimer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StaticHighResolutionTimerTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $timer = new StaticHighResolutionTimer(42);
        for ($i = 0; $i < 100; ++$i) {
            self::assertSame(42, $timer->now());
        }
    }
}
