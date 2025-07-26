<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\Exception;

use PhoneBurner\SaltLite\Container\Exception\NotResolvable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NotResolvableTest extends TestCase
{
    #[Test]
    public function exceptionMessageContainsClassName(): void
    {
        $class = 'Some\\Class\\Name';
        $exception = new NotResolvable($class);

        self::assertSame($class . ' Must Be Set Explicitly in the Container', $exception->getMessage());
    }
}
