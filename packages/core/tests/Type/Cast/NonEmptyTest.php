<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Type\Cast;

use PhoneBurner\SaltLite\Type\Cast\NonEmptyCast;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NonEmptyTest extends TestCase
{
    #[Test]
    public function emptyStringThrowsDefaultException(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('String Must Not Be Empty');
        NonEmptyCast::string('');
    }

    #[Test]
    public function emptyStringThrowsException(): void
    {
        $exception = new \RuntimeException('Custom Exception');
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Custom Exception');
        NonEmptyCast::string('', $exception);
    }

    #[DataProvider('providesStringTestCases')]
    #[Test]
    public function stringReturnsExpectedValue(string $input): void
    {
        self::assertSame($input, NonEmptyCast::string($input));
    }

    public static function providesStringTestCases(): \Generator
    {
        yield ['432',];
        yield ["hello, world"];
        yield ['0'];
        yield ['0.0'];
    }
}
