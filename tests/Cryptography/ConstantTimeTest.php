<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConstantTimeTest extends TestCase
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

    #[Test]
    #[DataProvider('providesEncodingHappyPathTests')]
    public function happyPathEncodingAndDecoding(
        Encoding $encoding,
        bool $prefix,
        string $input,
        string $expected,
    ): void {
        self::assertSame($expected, ConstantTime::encode($encoding, $input, $prefix));
        self::assertSame($input, ConstantTime::decode($encoding, $expected));
    }

    public static function providesEncodingHappyPathTests(): \Generator
    {
        yield [Encoding::Hex, false, 'hello', '68656c6c6f'];
        yield [Encoding::Hex, true, 'hello', 'hex:68656c6c6f'];

        yield [Encoding::Base64, false, 'hello', 'aGVsbG8='];
        yield [Encoding::Base64, true, 'hello', 'base64:aGVsbG8='];

        yield [Encoding::Base64NoPadding, false, 'hello', 'aGVsbG8'];
        yield [Encoding::Base64NoPadding, true, 'hello', 'base64:aGVsbG8'];

        yield [Encoding::Base64Url, false, 'hello', 'aGVsbG8='];
        yield [Encoding::Base64Url, true, 'hello', 'base64url:aGVsbG8='];

        yield [Encoding::Base64UrlNoPadding, false, 'hello', 'aGVsbG8'];
        yield [Encoding::Base64UrlNoPadding, true, 'hello', 'base64url:aGVsbG8'];

        yield [Encoding::Hex, false, 'The Quick Brown Fox Jumps Over The Lazy Dog', '54686520517569636b2042726f776e20466f78204a756d7073204f76657220546865204c617a7920446f67'];
        yield [Encoding::Hex, true, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'hex:54686520517569636b2042726f776e20466f78204a756d7073204f76657220546865204c617a7920446f67'];

        yield [Encoding::Base64, false, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw=='];
        yield [Encoding::Base64, true, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'base64:VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw=='];

        yield [Encoding::Base64NoPadding, false, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw'];
        yield [Encoding::Base64NoPadding, true, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'base64:VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw'];

        yield [Encoding::Base64Url, false, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw=='];
        yield [Encoding::Base64Url, true, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'base64url:VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw=='];

        yield [Encoding::Base64UrlNoPadding, false, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw'];
        yield [Encoding::Base64UrlNoPadding, true, 'The Quick Brown Fox Jumps Over The Lazy Dog', 'base64url:VGhlIFF1aWNrIEJyb3duIEZveCBKdW1wcyBPdmVyIFRoZSBMYXp5IERvZw'];

        yield [Encoding::Hex , false, '📞🔥', 'f09f939ef09f94a5'];
        yield [Encoding::Base64, false, '📞🔥', '8J+TnvCflKU='];
        yield [Encoding::Base64NoPadding, false, '📞🔥', '8J+TnvCflKU'];
        yield [Encoding::Base64Url, false, '📞🔥', '8J-TnvCflKU='];
        yield [Encoding::Base64UrlNoPadding, false, '📞🔥', '8J-TnvCflKU'];

        yield [Encoding::Hex , false, "\xff\xff\xfe\xff", 'fffffeff'];
        yield [Encoding::Base64, false, "\xff\xff\xfe\xff", '///+/w=='];
        yield [Encoding::Base64NoPadding, false, "\xff\xff\xfe\xff", '///+/w'];
        yield [Encoding::Base64Url, false, "\xff\xff\xfe\xff", '___-_w=='];
        yield [Encoding::Base64UrlNoPadding, false, "\xff\xff\xfe\xff", '___-_w'];
    }

    #[Test]
    #[DataProvider('providesInvalidInputForDecoding')]
    public function decodeThrowsExceptionOnInvalidInput(
        Encoding $encoding,
        string $input,
    ): void {
        $this->expectException(\UnexpectedValueException::class);
        ConstantTime::decode($encoding, $input);
    }

    public static function providesInvalidInputForDecoding(): \Generator
    {
        yield [Encoding::Hex, 'invalid'];
        yield [Encoding::Hex, '68656c6c6'];
        yield [Encoding::Base64, 'this is an invalid base64 string!'];
        yield [Encoding::Base64NoPadding, 'this is an invalid base64 string!'];
        yield [Encoding::Base64Url, 'this is an invalid base64 string!'];
        yield [Encoding::Base64UrlNoPadding, 'this is an invalid base64 string!'];
    }

    #[Test]
    public function hexPrefixesAreStripped(): void
    {
        self::assertSame('hello', ConstantTime::decode(Encoding::Hex, 'hex:68656c6c6f'));
        self::assertSame('hello', ConstantTime::decode(Encoding::Hex, '0x68656c6c6f'));
        self::assertSame('hello', ConstantTime::decode(Encoding::Hex, 'hex:0x68656c6c6f'));
    }
}
