<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use PhoneBurner\SaltLite\Attribute\Usage\Internal;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InternalTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $sut = new Internal();
        self::assertSame('', $sut->help);

        $sut = new Internal('This is a test');
        self::assertSame('This is a test', $sut->help);
    }
}
