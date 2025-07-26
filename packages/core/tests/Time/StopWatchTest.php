<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time;

use PhoneBurner\SaltLite\Time\StopWatch;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StopWatchTest extends TestCase
{
    #[Test]
    public function elapsedReturnsTheDuration(): void
    {
        $stopwatch = StopWatch::start();
        self::assertLessThan(1, $stopwatch->elapsed()->inSeconds());

        \sleep(1);
        $elapsed = $stopwatch->elapsed();
        self::assertGreaterThanOrEqual(1, $elapsed->inSeconds());
        self::assertLessThan(1.2, $elapsed->inSeconds());

        \sleep(1);
        $elapsed = $stopwatch->elapsed();
        self::assertGreaterThanOrEqual(2, $elapsed->inSeconds());
        self::assertLessThan(2.2, $elapsed->inSeconds());
    }
}
