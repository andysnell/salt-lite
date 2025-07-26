<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem;

use PhoneBurner\SaltLite\Filesystem\FileMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileModeTest extends TestCase
{
    #[Test]
    public function enumHasCorrectValues(): void
    {
        self::assertSame('r', FileMode::Read->value);
        self::assertSame('r+', FileMode::ReadWriteOverwriteExisting->value);
        self::assertSame('w', FileMode::WriteCreateOrTruncateExisting->value);
        self::assertSame('w+', FileMode::ReadWriteCreateOrTruncateExisting->value);
        self::assertSame('a', FileMode::WriteCreateOrAppendExisting->value);
        self::assertSame('a+', FileMode::ReadWriteCreateOrAppendExisting->value);
        self::assertSame('x', FileMode::WriteCreateOnly->value);
        self::assertSame('x+', FileMode::ReadWriteCreateOnly->value);
        self::assertSame('c', FileMode::WriteCreateOrOverwriteExisting->value);
        self::assertSame('c+', FileMode::ReadWriteCreateOrOverwriteExisting->value);
    }

    #[Test]
    #[DataProvider('stringCastProvider')]
    public function castConvertsStringToEnum(string $input, FileMode|null $expected): void
    {
        $result = FileMode::cast($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function castWithEnumReturnsEnum(): void
    {
        $mode = FileMode::Read;
        $result = FileMode::cast($mode);
        self::assertSame($mode, $result);
    }

    #[Test]
    public function castWithNullReturnsNull(): void
    {
        $result = FileMode::cast(null);
        self::assertNull($result);
    }

    #[Test]
    public function castWithStringableConvertsToEnum(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'r';
            }
        };

        $result = FileMode::cast($stringable);
        self::assertSame(FileMode::Read, $result);
    }

    #[Test]
    public function castWithUnsupportedTypeReturnsNull(): void
    {
        $result = FileMode::cast(123);
        self::assertNull($result);

        $result = FileMode::cast([]);
        self::assertNull($result);

        $result = FileMode::cast(new \stdClass());
        self::assertNull($result);
    }

    #[Test]
    #[DataProvider('instanceStringProvider')]
    public function instanceConvertsStringToEnum(string $input, FileMode $expected): void
    {
        $result = FileMode::instance($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function instanceThrowsForInvalidInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FileMode::instance('invalid');
    }

    #[Test]
    #[DataProvider('readableModeProvider')]
    public function isReadableReturnsCorrectValue(FileMode $mode, bool $expected): void
    {
        $result = $mode->isReadable();
        self::assertSame($expected, $result);
    }

    #[Test]
    #[DataProvider('writableModeProvider')]
    public function isWritableReturnsCorrectValue(FileMode $mode, bool $expected): void
    {
        $result = $mode->isWritable();
        self::assertSame($expected, $result);
    }

    /**
     * @return \Iterator<string, array{string, (FileMode | null)}>
     */
    public static function stringCastProvider(): \Iterator
    {
        yield 'r' => ['r', FileMode::Read];
        yield 'r+' => ['r+', FileMode::ReadWriteOverwriteExisting];
        yield 'w' => ['w', FileMode::WriteCreateOrTruncateExisting];
        yield 'w+' => ['w+', FileMode::ReadWriteCreateOrTruncateExisting];
        yield 'a' => ['a', FileMode::WriteCreateOrAppendExisting];
        yield 'a+' => ['a+', FileMode::ReadWriteCreateOrAppendExisting];
        yield 'x' => ['x', FileMode::WriteCreateOnly];
        yield 'x+' => ['x+', FileMode::ReadWriteCreateOnly];
        yield 'c' => ['c', FileMode::WriteCreateOrOverwriteExisting];
        yield 'c+' => ['c+', FileMode::ReadWriteCreateOrOverwriteExisting];
        yield 'uppercase R' => ['R', FileMode::Read];
        yield 'with b flag' => ['rb', FileMode::Read];
        yield 'with t flag' => ['rt', FileMode::Read];
        yield 'with both flags' => ['r+bt', FileMode::ReadWriteOverwriteExisting];
        yield 'invalid mode' => ['q', null];
        yield 'empty string' => ['', null];
    }

    /**
     * @return \Iterator<string, array{string, FileMode}>
     */
    public static function instanceStringProvider(): \Iterator
    {
        yield 'r' => ['r', FileMode::Read];
        yield 'r+' => ['r+', FileMode::ReadWriteOverwriteExisting];
        yield 'w' => ['w', FileMode::WriteCreateOrTruncateExisting];
        yield 'w+' => ['w+', FileMode::ReadWriteCreateOrTruncateExisting];
        yield 'a' => ['a', FileMode::WriteCreateOrAppendExisting];
        yield 'a+' => ['a+', FileMode::ReadWriteCreateOrAppendExisting];
        yield 'x' => ['x', FileMode::WriteCreateOnly];
        yield 'x+' => ['x+', FileMode::ReadWriteCreateOnly];
        yield 'c' => ['c', FileMode::WriteCreateOrOverwriteExisting];
        yield 'c+' => ['c+', FileMode::ReadWriteCreateOrOverwriteExisting];
        yield 'uppercase R' => ['R', FileMode::Read];
        yield 'with b flag' => ['rb', FileMode::Read];
        yield 'with t flag' => ['rt', FileMode::Read];
        yield 'with both flags' => ['r+bt', FileMode::ReadWriteOverwriteExisting];
    }

    /**
     * @return \Iterator<string, array{FileMode, bool}>
     */
    public static function readableModeProvider(): \Iterator
    {
        yield 'Read' => [FileMode::Read, true];
        yield 'ReadWriteOverwriteExisting' => [FileMode::ReadWriteOverwriteExisting, true];
        yield 'WriteCreateOrTruncateExisting' => [FileMode::WriteCreateOrTruncateExisting, false];
        yield 'ReadWriteCreateOrTruncateExisting' => [FileMode::ReadWriteCreateOrTruncateExisting, true];
        yield 'WriteCreateOrAppendExisting' => [FileMode::WriteCreateOrAppendExisting, false];
        yield 'ReadWriteCreateOrAppendExisting' => [FileMode::ReadWriteCreateOrAppendExisting, true];
        yield 'WriteCreateOnly' => [FileMode::WriteCreateOnly, false];
        yield 'ReadWriteCreateOnly' => [FileMode::ReadWriteCreateOnly, true];
        yield 'WriteCreateOrOverwriteExisting' => [FileMode::WriteCreateOrOverwriteExisting, false];
        yield 'ReadWriteCreateOrOverwriteExisting' => [FileMode::ReadWriteCreateOrOverwriteExisting, true];
    }

    /**
     * @return \Iterator<string, array{FileMode, bool}>
     */
    public static function writableModeProvider(): \Iterator
    {
        yield 'Read' => [FileMode::Read, false];
        yield 'ReadWriteOverwriteExisting' => [FileMode::ReadWriteOverwriteExisting, true];
        yield 'WriteCreateOrTruncateExisting' => [FileMode::WriteCreateOrTruncateExisting, true];
        yield 'ReadWriteCreateOrTruncateExisting' => [FileMode::ReadWriteCreateOrTruncateExisting, true];
        yield 'WriteCreateOrAppendExisting' => [FileMode::WriteCreateOrAppendExisting, true];
        yield 'ReadWriteCreateOrAppendExisting' => [FileMode::ReadWriteCreateOrAppendExisting, true];
        yield 'WriteCreateOnly' => [FileMode::WriteCreateOnly, true];
        yield 'ReadWriteCreateOnly' => [FileMode::ReadWriteCreateOnly, true];
        yield 'WriteCreateOrOverwriteExisting' => [FileMode::WriteCreateOrOverwriteExisting, true];
        yield 'ReadWriteCreateOrOverwriteExisting' => [FileMode::ReadWriteCreateOrOverwriteExisting, true];
    }
}
