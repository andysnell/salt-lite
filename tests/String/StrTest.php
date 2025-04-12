<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\String;

use Generator;
use Laminas\Diactoros\StreamFactory;
use PhoneBurner\SaltLite\Exception\NotInstantiable;
use PhoneBurner\SaltLite\String\RegExp;
use PhoneBurner\SaltLite\String\Str;
use PhoneBurner\SaltLite\Tests\Fixtures\ShinyThing;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use stdClass;
use Stringable;

final class StrTest extends TestCase
{
    #[Test]
    public function strCannotBeInstantiated(): void
    {
        $this->expectException(NotInstantiable::class);
        new Str();
    }

    #[DataProvider('providesValidStringTestCases')]
    #[Test]
    public function stringableWillReturnTrueForStringsAndStringableObjects(
        string $expected,
        string|\Stringable $test,
    ): void {
        self::assertTrue(Str::stringable($test));
    }

    #[DataProvider('providesInvalidStringTestCases')]
    #[Test]
    public function stringableWillReturnFalseForNonStringsOrStringableObjects(mixed $test): void
    {
        self::assertFalse(Str::stringable($test));
    }

    #[DataProvider('providesValidStringTestCases')]
    #[Test]
    public function stringWillCastStringlikeThingToString(string $expected, string|\Stringable $test): void
    {
        self::assertSame($expected, Str::cast($test));
    }

    #[Test]
    public function streamWillReturnPassedInstanceIfStreamInterface(): void
    {
        $stream = $this->createMock(StreamInterface::class);

        self::assertSame($stream, Str::stream($stream));
    }

    #[Test]
    public function streamDefaultValueReturnsEmptyStream(): void
    {
        self::assertSame('', (string)Str::stream());
    }

    #[DataProvider('providesValidStringTestCases')]
    #[Test]
    public function streamWillCastStringOrStringableToStream(string $expected, string|Stringable $test): void
    {
        $stream = Str::stream($test);

        self::assertInstanceOf(StreamInterface::class, $stream);
        self::assertSame($expected, $stream->getContents());
    }

    public static function providesValidStringTestCases(): Generator
    {
        yield 'string' => ['Hello, World', 'Hello, World'];

        yield Stringable::class => ['One Two Three', new class implements Stringable {
            public function __toString(): string
            {
                return 'One Two Three';
            }
        },];

        yield '__toString' => ['Foo Bar Baz', new class {
            public function __toString(): string
            {
                return "Foo Bar Baz";
            }
        },];

        $test = 'StreamInterface Implements __toString';
        yield 'stream' => [$test, new StreamFactory()->createStream($test)];
    }

    public static function providesInvalidStringTestCases(): Generator
    {
        yield 'null' => [null];
        yield 'true' => [true];
        yield 'false' => [false];
        yield 'zero' => [0];
        yield 'int' => [1];
        yield 'float' => [1.2];
        yield 'object' => [new stdClass()];
        yield 'empty_array' => [[]];
        yield 'array' => [['foo' => 'bar', 'baz' => 'quz']];

        $resource = \fopen('php://temp', 'rb+') ?: self::fail();
        \fwrite($resource, 'Hello World');
        \rewind($resource);

        yield 'resource' => [$resource];
    }

    /**
     * @param array{input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesTrimTestCases')]
    #[Test]
    public function trimWillTrimWhitespaceCharacters(array $test): void
    {
        $trimmed = Str::trim($test['input']);
        self::assertSame($test['trim'], $trimmed);
    }

    /**
     * @param array{ characters: array<string>, input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesAdditionalCharacterTrimTestCases')]
    #[Test]
    public function trimWillTrimAdditionalCharacters(array $test): void
    {
        $trimmed = Str::trim($test['input'], $test['characters']);
        self::assertSame($test['trim'], $trimmed);
    }

    /**
     * @param array{input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesTrimTestCases')]
    #[Test]
    public function rtrimWillTrimWhitespaceCharacters(array $test): void
    {
        $trimmed = Str::rtrim($test['input']);
        self::assertSame($test['rtrim'], $trimmed);
    }

    /**
     * @param array{ characters: array<string>, input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesAdditionalCharacterTrimTestCases')]
    #[Test]
    public function rtrimWillTrimAdditionalCharacters(array $test): void
    {
        $trimmed = Str::rtrim($test['input'], $test['characters']);
        self::assertSame($test['rtrim'], $trimmed);
    }

    /**
     * @param array{input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesTrimTestCases')]
    #[Test]
    public function ltrimWillTrimWhitespaceCharacters(array $test): void
    {
        $trimmed = Str::ltrim($test['input']);
        self::assertSame($test['ltrim'], $trimmed);
    }

    /**
     * @param array{ characters: array<string>, input: string, trim: string, rtrim: string, ltrim: string} $test
     */
    #[DataProvider('providesAdditionalCharacterTrimTestCases')]
    #[Test]
    public function ltrimWillTrimAdditionalCharacters(array $test): void
    {
        $trimmed = Str::ltrim($test['input'], $test['characters']);
        self::assertSame($test['ltrim'], $trimmed);
    }

    /**
     * @return Generator<string, array{0: array{
     *     input: string,
     *     trim: string,
     *     rtrim: string,
     *     ltrim: string,
     * }}>
     */
    public static function providesTrimTestCases(): Generator
    {
        yield 'no_trim' => [[
            'input' => 'Hello, World!',
            'trim' => 'Hello, World!',
            'rtrim' => 'Hello, World!',
            'ltrim' => 'Hello, World!',
        ]];

        yield 'spaces_trim' => [[
            'input' => '  Hello, World!  ',
            'trim' => 'Hello, World!',
            'rtrim' => '  Hello, World!',
            'ltrim' => 'Hello, World!  ',
        ]];

        yield 'line_breaks_trim' => [[
            'input' => "\n\n\r Hello, World! \r\r\n",
            'trim' => "Hello, World!",
            'rtrim' => "\n\n\r Hello, World!",
            'ltrim' => "Hello, World! \r\r\n",
        ]];

        yield 'all_the_whitespace' => [[
            'input' => " \t\n\r\0\x0B \t\n\r\0\x0BHello, \t\n\r\0\x0B World! \t\n\r\0\x0B \t\n\r\0\x0B",
            'trim' => "Hello, \t\n\r\0\x0B World!",
            'rtrim' => " \t\n\r\0\x0B \t\n\r\0\x0BHello, \t\n\r\0\x0B World!",
            'ltrim' => "Hello, \t\n\r\0\x0B World! \t\n\r\0\x0B \t\n\r\0\x0B",
        ]];
    }

    /**
     * @return Generator<string, array{0: array{
     *     characters: array<string>,
     *     input: string,
     *     trim: string,
     *     rtrim: string,
     *     ltrim: string,
     *     }}>
     */
    public static function providesAdditionalCharacterTrimTestCases(): Generator
    {
        foreach (self::providesTrimTestCases() as $test_name => $test) {
            \assert(\is_array($test));
            $test[0]['characters'] = [];
            yield $test_name . '_no_chars' => $test;
        }

        yield 'trim_everything' => [[
            'characters' => \str_split('Hello, World!'),
            'input' => 'Hello, World!',
            'trim' => '',
            'rtrim' => '',
            'ltrim' => '',
        ]];

        yield 'trim_almost_everything' => [[
            'characters' => \str_split('Hello, World!'),
            'input' => 'Hello, | World!',
            'trim' => '|',
            'rtrim' => 'Hello, |',
            'ltrim' => '| World!',
        ]];

        yield 'all_the_whitespace_with_symbol' => [[
            'characters' => ['$'],
            'input' => " \t\n\r\0\x0B \t\n\r\0\x0B$12.42\t\n\r\0\x0B \t\n\r\0\x0B",
            'trim' => "12.42",
            'rtrim' => " \t\n\r\0\x0B \t\n\r\0\x0B$12.42",
            'ltrim' => "12.42\t\n\r\0\x0B \t\n\r\0\x0B",
        ]];

        yield 'trim_quotes_single' => [[
            'characters' => ['"', "'"],
            'input' => "'Hello, World!'",
            'trim' => 'Hello, World!',
            'rtrim' => "'Hello, World!",
            'ltrim' => "Hello, World!'",
        ]];

        yield 'trim_quotes_double' => [[
            'characters' => ['"', "'"],
            'input' => '"Hello, World!"',
            'trim' => 'Hello, World!',
            'rtrim' => '"Hello, World!',
            'ltrim' => 'Hello, World!"',
        ]];
    }

    /**
     * @param array{haystack: string, expected: bool, needle: string, case_sensitive: bool} $test
     */
    #[DataProvider('providesContainsTestCases')]
    #[Test]
    public function containsReturnsIfStringContainsString(array $test): void
    {
        self::assertSame($test['expected'], Str::contains(
            $test['haystack'],
            $test['needle'],
            $test['case_sensitive'],
        ));
    }

    /**
     * @return \Generator<array<array{ haystack: string, expected: bool, needle: string, case_sensitive: bool}>>
     */
    public static function providesContainsTestCases(): \Generator
    {
        $test = static function (bool $expected, bool $case_sensitive, string $needle, string|null $haystack = null): array {
            $haystack ??= 'The Quick Brown Fox Jumped Over The Lazy Dog.';
            return [['haystack' => $haystack, 'expected' => $expected, 'needle' => $needle, 'case_sensitive' => $case_sensitive]];
        };

        // PHP 8's new str_contains function always returns true when needle is empty
        yield $test(true, true, '');
        yield $test(true, true, 'Brown Fox');
        yield $test(true, true, 'The Quick Brown Fox');
        yield $test(true, true, 'The Quick Brown Fox Jumped Over The Lazy Dog.');
        yield $test(true, true, 'The Lazy Dog.');
        yield $test(false, true, 'BROWN FOX');
        yield $test(false, true, 'THE QUICK BROWN FOX');
        yield $test(false, true, 'THE QUICK BROWN FOX JUMPED OVER THE LAZY DOG.');
        yield $test(false, true, 'THE LAZY DOG.');
        yield $test(false, true, 'brown fox');
        yield $test(false, true, 'the quick brown fox');
        yield $test(false, true, 'the quick brown fox jumped over the lazy dog.');
        yield $test(false, true, 'the lazy dog.');
        yield $test(true, false, 'BROWN FOX');
        yield $test(true, false, 'THE QUICK BROWN FOX');
        yield $test(true, false, 'THE QUICK BROWN FOX JUMPED OVER THE LAZY DOG.');
        yield $test(true, false, 'THE LAZY DOG.');
        yield $test(true, false, 'brown fox');
        yield $test(true, false, 'the quick brown fox');
        yield $test(true, false, 'the quick brown fox jumped over the lazy dog.');
        yield $test(true, false, 'the lazy dog.');
        yield $test(false, false, 'quick fox');
        yield $test(false, true, 'quick fox');
        yield $test(false, false, 'QUICK FOX');
        yield $test(false, true, 'QUICK FOX');
        yield $test(false, false, 'foo', '');
        yield $test(true, true, ' ');
        yield $test(true, false, ' ');
        yield $test(false, false, 'foo', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(false, true, 'foo', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, true, '😺 💷 📅', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, false, '😺 💷 📅', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, true, '🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, false, '🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(false, true, '🐌 🅾️ 😺 💷 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(false, false, '🐌 🅾️ 😺 💷 📅 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, false, '');
        yield $test(true, true, '', '');
        yield $test(true, false, '', '');
        yield $test(true, true, '', 'foo');
        yield $test(true, false, '', 'foo');
    }

    /**
     * @param array{haystack: string, expected: bool, needle: string, case_sensitive: bool} $test
     */
    #[DataProvider('providesStartsWithTestCases')]
    #[Test]
    public function startsWithReturnsIfStringStartsWithString(array $test): void
    {
        self::assertSame($test['expected'], Str::startsWith(
            $test['haystack'],
            $test['needle'],
            $test['case_sensitive'],
        ));
    }

    /**
     * @return Generator<array<array{haystack: string, expected: bool, needle: string, case_sensitive:bool}>>
     */
    public static function providesStartsWithTestCases(): Generator
    {
        $test = static function (bool $expected, bool $case_sensitive, string $needle, string|null $haystack = null): array {
            $haystack ??= 'The Quick Brown Fox Jumped Over The Lazy Dog.';
            return [['haystack' => $haystack, 'expected' => $expected, 'needle' => $needle, 'case_sensitive' => $case_sensitive]];
        };

        // PHP 8's new string_ends_with function always returns true when needle is empty
        yield $test(true, true, '');
        yield $test(true, true, 'T');
        yield $test(true, true, 'The');
        yield $test(true, true, 'The Quick Brown Fox');
        yield $test(true, true, 'The Quick Brown Fox Jumped Over The Lazy Dog.');
        yield $test(true, false, 'T');
        yield $test(true, false, 'The');
        yield $test(true, false, 'The Quick Brown Fox');
        yield $test(true, false, 'The Quick Brown Fox Jumped Over The Lazy Dog.');
        yield $test(true, false, 't');
        yield $test(true, false, 'the');
        yield $test(true, false, 'the quick brown fox');
        yield $test(true, false, 'the quick brown fox jumped over the lazy dog.');
        yield $test(false, true, 't');
        yield $test(false, true, 'the');
        yield $test(false, true, 'the quick brown fox');
        yield $test(false, true, 'the quick brown fox jumped over the lazy dog.');
        yield $test(true, true, '🍩 🏒 🎯 🍣 ⏳ 📀 🐌', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(true, false, '🍩 🏒 🎯 🍣 ⏳ 📀 🐌', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(false, true, '🏒 🎯 🍣 ⏳ 📀 🐌', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
        yield $test(false, false, '🏒 🎯 🍣 ⏳ 📀 🐌', '🍩 🏒 🎯 🍣 ⏳ 📀 🐌 🅾️ 😺 💷 📅 🔋 🌴 ⛷ 💣 💚 🌄 ⚡️ ⚫️ ↙️');
    }

    /**
     * @param array{haystack: string, expected: bool, needle: string, case_sensitive: bool} $test
     */
    #[DataProvider('providesEndsWithTestCases')]
    #[Test]
    public function endsWithReturnsIfStringEndsWithString(array $test): void
    {
        self::assertSame($test['expected'], Str::endsWith(
            $test['haystack'],
            $test['needle'],
            $test['case_sensitive'],
        ));
    }

    /**
     * @return Generator<array<array{haystack: string, expected: bool, needle: string, case_sensitive:bool}>>
     */
    public static function providesEndsWithTestCases(): Generator
    {
        $test = static function (bool $expected, bool $case_sensitive, string $needle, string|null $haystack = null): array {
            $haystack ??= 'The Quick Brown Fox Jumped Over The Lazy Dog.';
            return [['haystack' => $haystack, 'expected' => $expected, 'needle' => $needle, 'case_sensitive' => $case_sensitive]];
        };

        // PHP 8's new string_ends_with function always returns true when needle is empty
        yield $test(true, true, '');
        yield $test(true, true, '.');
        yield $test(true, true, 'Dog.');
        yield $test(true, true, 'Lazy Dog.');
        yield $test(true, true, 'The Quick Brown Fox Jumped Over The Lazy Dog.');
        yield $test(true, false, '');
        yield $test(true, false, '.');
        yield $test(true, false, 'DoG.');
        yield $test(true, false, 'LAZY dog.');
        yield $test(false, true, 'DoG.');
        yield $test(false, true, 'LAZY dog.');
        yield $test(false, true, ' The Quick Brown Fox Jumped Over The Lazy Dog.');
        yield $test(true, false, '', '');
        yield $test(true, true, '', '');
        yield $test(true, false, '', 'foo');
        yield $test(true, true, '', '');
        yield $test(true, true, '😃', 'Hello, World! 😃');
        yield $test(true, true, '! 👻😃👻', 'Hello, World! 👻😃👻');
        yield $test(false, true, '! 👻😃👻', 'Hello, World! 👻😀👻'); // slightly different emoji
        yield $test(true, false, 'D! 👻😃👻', 'Hello, World! 👻😃👻');
    }

    /**
     * @param array{input: string, prefix: string, expected: string} $test
     */
    #[DataProvider('providesStartTestCases')]
    #[Test]
    public function startPrependsIfStringDoesNotStartWithValue(array $test): void
    {
        self::assertSame($test['expected'], Str::start($test['input'], $test['prefix']));
    }

    /**
     * @return Generator<array<array{ input: string, prefix: string, expected: string}>>
     */
    public static function providesStartTestCases(): Generator
    {
        $test = static fn($input, $prefix, $expected): array => [['input' => $input, 'prefix' => $prefix, 'expected' => $expected]];

        yield $test('', '', '');
        yield $test('/path/to/something', '/', '/path/to/something');
        yield $test('path/to/something', '/', '/path/to/something');
        yield $test('https://www.example.com', 'https://www.example.com', 'https://www.example.com');
        yield $test('', 'https://www.example.com', 'https://www.example.com');
        yield $test('www.example.com', 'https://', 'https://www.example.com');
        yield $test('https://www.example.com', '', 'https://www.example.com');
        yield $test('📍️🎳📡😘🏕', '📍️🎳📡😘🏕', '📍️🎳📡😘🏕');
        yield $test('📡😘🏕', '📍️🎳', '📍️🎳📡😘🏕');
    }

    /**
     * @param array{input: string, suffix: string, expected: string} $test
     */
    #[DataProvider('providesEndTestCases')]
    #[Test]
    public function endAppendsIfStringDoesNotEndWithValue(array $test): void
    {
        self::assertSame($test['expected'], Str::end($test['input'], $test['suffix']));
    }

    /**
     * @return Generator<array<array{input: string, suffix: string, expected: string}>>
     */
    public static function providesEndTestCases(): Generator
    {
        $test = static fn($input, $suffix, $expected): array => [['input' => $input, 'suffix' => $suffix, 'expected' => $expected]];

        yield $test('', '', '');
        yield $test('path/to/something', '/', 'path/to/something/');
        yield $test('path/to/something/', '/', 'path/to/something/');
        yield $test('/path/to/something//', '/', '/path/to/something//');
        yield $test('https://www.example.com/', 'https://www.example.com/', 'https://www.example.com/');
        yield $test('', 'https://www.example.com', 'https://www.example.com');
        yield $test('www.example.com', '/path?query=foo', 'www.example.com/path?query=foo');
        yield $test('www.example.com/path?query=foo', '/path?query=foo', 'www.example.com/path?query=foo');
        yield $test('https://www.example.com', '', 'https://www.example.com');
        yield $test('⏬🎨💠🗒🔲🐊', '🏵🚾💂🍶📟🔍', '⏬🎨💠🗒🔲🐊🏵🚾💂🍶📟🔍');
        yield $test('⏬🎨💠🗒🔲🐊', '🐊', '⏬🎨💠🗒🔲🐊');
        yield $test('⏬🎨💠🗒🔲🐊 ', '🐊', '⏬🎨💠🗒🔲🐊 🐊');
    }

    #[DataProvider('providesStripTestByStringCases')]
    #[Test]
    public function stripRemovesExpectedCharacters(string $string, string $search, string $expected): void
    {
        self::assertSame($expected, Str::strip($string, $search));
    }

    /**
     * @return Generator<array{0: string, 1: string, 2: string}>
     */
    public static function providesStripTestByStringCases(): Generator
    {
        yield ['', '', ''];
        yield ['a', 'a', ''];
        yield ['Hello, World', '', 'Hello, World'];
        yield ['Hello, World', 'a', 'Hello, World'];
        yield ['Hello, World', 'l', 'Heo, Word'];
        yield ['Hello, World', 'Hello, World', ''];
        yield ['a⏬🎨💠🗒🔲🐊', 'a⏬🎨💠🗒🔲🐊', ''];
        yield ['a⏬🎨💠🗒🔲🐊', 'a', '⏬🎨💠🗒🔲🐊'];
        yield ['a⏬🎨💠🗒🔲🐊', '⏬🎨💠🗒🔲🐊', 'a'];
        yield ['👻😃👻', '👻', '😃'];
        yield ['👻😃👻', '😃', '👻👻'];
        yield [' The Quick Brown Fox Jumped Over The Lazy Dog. ', ' ', 'TheQuickBrownFoxJumpedOverTheLazyDog.'];
        yield [<<<'TAG'
            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed 
            do eiusmod tempor incididunt ut labore et dolore magna aliqua. 
            Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris 
            nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in 
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla 
            pariatur. Excepteur sint occaecat cupidatat non proident, sunt in 
            culpa qui officia deserunt mollit anim id est laborum.
            TAG, \PHP_EOL, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',];
    }

    #[DataProvider('providesStripTestCasesByRegExp')]
    #[Test]
    public function stripRemovesExpectedCharactersByRegexp(string $string, RegExp $regexp, string $expected): void
    {
        self::assertSame($expected, Str::strip($string, $regexp));
    }

    /**
     * @return Generator<array{0: string, 1: RegExp, 2: string}>
     */
    public static function providesStripTestCasesByRegExp(): Generator
    {
        yield ['', RegExp::make(''), ''];
        yield ['a', RegExp::make('a'), ''];
        yield ['aa', RegExp::make('a'), ''];
        yield ['Aa', RegExp::make('a'), 'A'];
        yield ['Aa', RegExp::make('a', 'i'), ''];
    }

    #[Test]
    public function stripThrowsExceptionIfRegexpIsNotProvided(): void
    {
        $this->expectException(\RuntimeException::class);
        Str::strip('foo', new RegExp('#foo/'));
    }

    #[DataProvider('providesShortnameTestCases')]
    #[Test]
    public function shortnameReturnsClassNameWithoutNamespace(string $expected, string $classname): void
    {
        self::assertSame($expected, Str::shortname($classname));
    }

    /**
     * @return Generator<string, array{0: string, 1: string}>
     */
    public static function providesShortnameTestCases(): Generator
    {
        $expected = 'ShinyThing';
        yield 'fully-qualified' => [$expected, '\\' . ShinyThing::class];
        yield 'qualified' => [$expected, ShinyThing::class];
        yield 'relative' => [$expected, 'namespace\Entity\DailyMetrics\ShinyThing'];
        yield 'shortname' => [$expected, 'ShinyThing'];
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function snakeCovertsStringToSnakeCase(array $expected, string $input): void
    {
        self::assertSame($expected['snake'], Str::snake($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function kabobCovertsStringToKabobCase(array $expected, string $input): void
    {
        self::assertSame($expected['kabob'], Str::kabob($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function pascalCovertsStringToPascalCase(array $expected, string $input): void
    {
        self::assertSame($expected['pascal'], Str::pascal($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function camelCovertsStringToCamelCase(array $expected, string $input): void
    {
        self::assertSame($expected['camel'], Str::camel($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function screamingCovertsStringToScreamingSnakeCase(array $expected, string $input): void
    {
        self::assertSame($expected['screaming'], Str::screaming($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function dotCovertsStringToDotCase(array $expected, string $input): void
    {
        self::assertSame($expected['dot'], Str::dot($input));
    }

    /**
     * @param array{snake: string, screaming: string, kabob: string, pascal: string, camel: string, dot: string,
     *     ucwords: string} $expected
     */
    #[DataProvider('providesStringCaseConversionTestCases')]
    #[Test]
    public function ucwordsCovertsStringToUcwordsCase(array $expected, string $input): void
    {
        self::assertSame($expected['ucwords'], Str::ucwords($input));
    }

    /**
     * @return Generator<array{0: array<string, string>, 1: string}>
     */
    public static function providesStringCaseConversionTestCases(): Generator
    {
        $expected = [
            'snake' => 'foo',
            'screaming' => 'FOO',
            'kabob' => 'foo',
            'pascal' => 'Foo',
            'camel' => 'foo',
            'dot' => 'foo',
            'ucwords' => 'Foo',
        ];
        foreach ($expected as $input) {
            yield [$expected, $input];
        }

        $expected = [
            'snake' => 'the_quick_brown_fox_jumped_over_the_lazy_dog',
            'screaming' => 'THE_QUICK_BROWN_FOX_JUMPED_OVER_THE_LAZY_DOG',
            'kabob' => 'the-quick-brown-fox-jumped-over-the-lazy-dog',
            'pascal' => 'TheQuickBrownFoxJumpedOverTheLazyDog',
            'camel' => 'theQuickBrownFoxJumpedOverTheLazyDog',
            'dot' => 'the.quick.brown.fox.jumped.over.the.lazy.dog',
            'ucwords' => 'The Quick Brown Fox Jumped Over The Lazy Dog',
        ];

        foreach ($expected as $input) {
            yield [$expected, $input];
        }
        yield [$expected, 'The Quick Brown Fox Jumped Over The Lazy Dog'];
        yield [$expected, ' The    Quick   Brown   Fox    Jumped   Over   The   Lazy   Dog   '];
        yield [$expected, 'TheQuickBrownFoxJUMPEDOverTheLazyDog'];
        yield [$expected, ' TheQuickBrownFoxJUMPEDOverTheLazyDog '];
        yield [$expected, ' TheQuickBrownFoxJUMPEDOverTheLazyDog. '];
        yield [$expected, ' TheQUICKBrownFoxJUMPEDOverTheLazyDog. '];
        yield [$expected, ' theQUICKBrownFoxJUMPEDOverTheLazyDog. '];
        yield [$expected, ' TheQuickBrown_FoxJUMPEDOverThe_LazyDog. '];

        $expected = [
            'snake' => 'thequickbrownfoxjumpedoverthelazydog',
            'screaming' => 'THEQUICKBROWNFOXJUMPEDOVERTHELAZYDOG',
            'kabob' => 'thequickbrownfoxjumpedoverthelazydog',
            'pascal' => 'Thequickbrownfoxjumpedoverthelazydog',
            'camel' => 'thequickbrownfoxjumpedoverthelazydog',
            'dot' => 'thequickbrownfoxjumpedoverthelazydog',
            'ucwords' => 'Thequickbrownfoxjumpedoverthelazydog',
        ];

        yield [$expected, 'THEQUICKBROWNFOXJUMPEDOVERTHELAZYDOG'];
        yield [$expected, 'thequickbrownfoxjumpedoverthelazydog'];

        $expected = [
            'snake' => 'some4_numbers234',
            'screaming' => 'SOME4_NUMBERS234',
            'kabob' => 'some4-numbers234',
            'pascal' => 'Some4Numbers234',
            'camel' => 'some4Numbers234',
            'dot' => 'some4.numbers234',
            'ucwords' => 'Some4 Numbers234',
        ];

        yield [$expected, 'Some4Numbers234'];

        $expected = [
            'snake' => 'some_4_numbers_234',
            'screaming' => 'SOME_4_NUMBERS_234',
            'kabob' => 'some-4-numbers-234',
            'pascal' => 'Some4Numbers234',
            'camel' => 'some4Numbers234',
            'dot' => 'some.4.numbers.234',
            'ucwords' => 'Some 4 Numbers 234',
        ];

        yield [$expected, 'Some 4 Numbers 234'];

        $expected = [
            'snake' => 'simple_xml',
            'screaming' => 'SIMPLE_XML',
            'kabob' => 'simple-xml',
            'pascal' => 'SimpleXml',
            'camel' => 'simpleXml',
            'dot' => 'simple.xml',
            'ucwords' => 'Simple Xml',
        ];

        yield [$expected, 'simpleXML'];
    }

    #[DataProvider('providesValidStringTestCases')]
    #[Test]
    public function objectReturnsStringableOfString(string $expected, mixed $input): void
    {
        $object = Str::object($input);

        self::assertInstanceOf(\Stringable::class, $object);
        self::assertSame($expected, (string)$object);
    }

    #[DataProvider('providesTruncateTestCases')]
    #[Test]
    public function truncateReturnsExpectedString(
        string|\Stringable $input,
        int $max_length,
        string $append,
        string $expected,
    ): void {
        self::assertSame($expected, Str::truncate($input, $max_length, $append));
    }

    public static function providesTruncateTestCases(): Generator
    {
        yield ['', 10, '', ''];
        yield ['', 10, '...', ''];
        yield ['Hello, world!', 13, '', 'Hello, world!'];
        yield ['Hello, world!', 13, '...', 'Hello, world!'];
        yield ['Hello, world!', 10, '', 'Hello, wor'];
        yield ['Hello, world!', 10, '...', 'Hello, ...'];
        yield ['Hello, world!', 3, '...', '...'];
        yield ['Hello, world!', 0, '', ''];
        yield [Str::object(''), 10, '', ''];
        yield [Str::object(''), 10, '...', ''];
        yield [Str::object('Hello, world!'), 13, '', 'Hello, world!'];
        yield [Str::object('Hello, world!'), 13, '...', 'Hello, world!'];
        yield [Str::object('Hello, world!'), 10, '', 'Hello, wor'];
        yield [Str::object('Hello, world!'), 10, '...', 'Hello, ...'];
        yield [Str::object('Hello, world!'), 3, '...', '...'];
        yield [Str::object('Hello, world!'), 0, '', ''];
    }

    #[Test]
    public function truncateEnforcesNonnegativeMaxLength(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Max Length Must Be Non-Negative');
        Str::truncate('Hello, world!', -1);
    }

    #[Test]
    public function truncateEnforcesMaxAppendLength(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Trim Marker Length Must Be Less Than or Equal to Max Length');
        Str::truncate('Hello, world!', 3, '....');
    }
}
