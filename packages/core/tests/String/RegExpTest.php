<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\String;

use PhoneBurner\SaltLite\String\RegExp;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RegExpTest extends TestCase
{
    #[DataProvider('regularExpressions')]
    #[Test]
    public function makeReturnsExpected(string $regexp, string $modifiers, string $expected): void
    {
        self::assertSame($expected, (string)RegExp::make($regexp, $modifiers));
    }

    public static function regularExpressions(): \Generator
    {
        yield ['[Aa]', '', '/[Aa]/'];
        yield ['[Aa]', 'i', '/[Aa]/i'];
        yield ['[Aa]', 'ig', '/[Aa]/ig'];
    }
}
