<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Routing\Result;

use LogicException;
use PhoneBurner\SaltLite\Http\Domain\HttpMethod;
use PhoneBurner\SaltLite\Http\Routing\Result\MethodNotAllowed;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MethodNotAllowedTest extends TestCase
{
    private array $methods;

    #[\Override]
    protected function setUp(): void
    {
        $this->methods = [HttpMethod::Post, HttpMethod::Put];
    }

    #[Test]
    public function makeReturnsFound(): void
    {
        $sut = MethodNotAllowed::make(...$this->methods);
        self::assertFalse($sut->isFound());
    }

    #[Test]
    public function makeDoesNotReturnRouteMatch(): void
    {
        $sut = MethodNotAllowed::make(...$this->methods);
        $this->expectException(LogicException::class);
        $sut->getRouteMatch();
    }
}
