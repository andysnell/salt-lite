<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use PhoneBurner\SaltLite\Attribute\Usage\RestrictTo;
use PhoneBurner\SaltLite\Container\ServiceProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;

final class RestrictToTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $sut = new RestrictTo(ServiceProvider::class);
        self::assertSame([ServiceProvider::class], $sut->classes);
    }

    #[Test]
    public function happyPathWithMultipleClasses(): void
    {
        $sut = new RestrictTo(ServiceProvider::class, ContainerInterface::class, RequestInterface::class);
        self::assertSame([
            ServiceProvider::class,
            ContainerInterface::class,
            RequestInterface::class,
        ], $sut->classes);
    }

    #[Test]
    public function sadPath(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        new RestrictTo();
    }
}
