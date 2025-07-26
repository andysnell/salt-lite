<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\String\Encoding;

use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EncodingTest extends TestCase
{
    #[Test]
    public function prefixReturnsExpectedValue(): void
    {
        self::assertSame('hex:', Encoding::Hex->prefix());
        self::assertSame('base64:', Encoding::Base64->prefix());
        self::assertSame('base64:', Encoding::Base64NoPadding->prefix());
        self::assertSame('base64url:', Encoding::Base64Url->prefix());
        self::assertSame('base64url:', Encoding::Base64UrlNoPadding->prefix());
    }

    #[Test]
    public function regexReturnsExpectedValue(): void
    {
        self::assertSame('/^[A-Fa-f0-9]+$/', Encoding::Hex->regex());
        self::assertSame('/^[A-Za-z0-9+\/]+={0,2}$/', Encoding::Base64->regex());
        self::assertSame('/^[A-Za-z0-9+\/]+$/', Encoding::Base64NoPadding->regex());
        self::assertSame('/^[A-Za-z0-9-_]+={0,2}$/', Encoding::Base64Url->regex());
        self::assertSame('/^[A-Za-z0-9-_]+$/', Encoding::Base64UrlNoPadding->regex());
    }
}
