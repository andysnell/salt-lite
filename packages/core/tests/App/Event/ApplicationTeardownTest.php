<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\App\Event;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\App\Event\ApplicationTeardown;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApplicationTeardownTest extends TestCase
{
    #[Test]
    public function constructorSetsAppProperty(): void
    {
        $app = $this->createMock(App::class);
        self::assertSame($app, new ApplicationTeardown($app)->app);
    }
}
