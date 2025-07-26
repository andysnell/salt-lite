<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem;

use PhoneBurner\SaltLite\Filesystem\FileReader;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use const PhoneBurner\SaltLite\UNIT_TEST_ROOT;

final class FileReaderTest extends TestCase
{
    #[Test]
    #[DataProvider('providesTestCases')]
    public function toStringReturnsFileContents(string|\Stringable $file): void
    {
        $reader = FileReader::make($file);
        self::assertStringEqualsFile(UNIT_TEST_ROOT . '/Fixtures/lorem.txt', (string)$reader);
    }

    #[Test]
    #[DataProvider('providesEmptyTestCases')]
    public function toStringReturnsFileContentsEmptyCase(string|\Stringable $file): void
    {
        $reader = FileReader::make($file);
        self::assertSame('', (string)$reader);
    }

    #[Test]
    #[DataProvider('providesTestCases')]
    public function iteratingReturnsFileContents(string|\Stringable $file): void
    {
        $reader = FileReader::make($file);
        self::assertStringEqualsFile(UNIT_TEST_ROOT . '/Fixtures/lorem.txt', \implode('', [...$reader]));
    }

    #[Test]
    #[DataProvider('providesEmptyTestCases')]
    public function iteratingReturnsFileContentsEmptyCase(string|\Stringable $file): void
    {
        $reader = FileReader::make($file);
        self::assertSame('', \implode('', [...$reader]));
    }

    #[Test]
    public function makeChecksIfFileExists(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FileReader::make(UNIT_TEST_ROOT . '/Fixtures/does-not-exist.txt');
    }

    public static function providesTestCases(): \Generator
    {
        yield [UNIT_TEST_ROOT . '/Fixtures/lorem.txt'];
        yield [new class implements \Stringable {
            public function __toString(): string
            {
                return UNIT_TEST_ROOT . '/Fixtures/lorem.txt';
            }
        }];
        yield [new \SplFileInfo(UNIT_TEST_ROOT . '/Fixtures/lorem.txt')];
        yield [new \SplFileObject(UNIT_TEST_ROOT . '/Fixtures/lorem.txt', 'r+b')];
    }

    public static function providesEmptyTestCases(): \Generator
    {
        yield [UNIT_TEST_ROOT . '/Fixtures/empty.txt'];
        yield [new class implements \Stringable {
            public function __toString(): string
            {
                return UNIT_TEST_ROOT . '/Fixtures/empty.txt';
            }
        }];
        yield [new \SplFileInfo(UNIT_TEST_ROOT . '/Fixtures/empty.txt')];
        yield [new \SplFileObject(UNIT_TEST_ROOT . '/Fixtures/empty.txt', 'r+b')];
    }
}
