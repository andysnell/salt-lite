<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem;

use PhoneBurner\SaltLite\Filesystem\FileWriter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileWriterTest extends TestCase
{
    #[\Override]
    protected function tearDown(): void
    {
        foreach ((array)\glob(__DIR__ . '/test.txt*') as $file) {
            @\unlink((string)$file);
        }
    }

    #[Test]
    public function stringWritesExpectedFile(): void
    {
        $contents = "foo\nbar\nbaz\n";
        $filename = __DIR__ . '/test.txt';
        self::assertFileDoesNotExist($filename);

        FileWriter::string($filename, $contents);

        self::assertFileExists($filename);
        self::assertSame([], \glob($filename . '.*'));
        self::assertSame($contents, (string)\file_get_contents($filename));
    }

    #[Test]
    public function stringOverwritesExpectedFile(): void
    {
        $old_contents = "qux\nquux\nquuz\n";
        $new_contents = "foo\nbar\nbaz\n";

        $filename = __DIR__ . '/test.txt';
        \file_put_contents($filename, $old_contents);
        self::assertFileExists($filename);
        self::assertSame($old_contents, (string)\file_get_contents($filename));

        FileWriter::string($filename, $new_contents);

        self::assertFileExists($filename);
        self::assertSame([], \glob($filename . '.*'));
        self::assertSame($new_contents, (string)\file_get_contents($filename));
    }

    #[Test]
    public function iterableWritesExpectedFile(): void
    {
        $iterable = static function (): \Generator {
            yield 'foo' . \PHP_EOL;
            yield 'bar' . \PHP_EOL;
            yield 'baz' . \PHP_EOL;
        };

        $filename = __DIR__ . '/test.txt';
        self::assertFileDoesNotExist($filename);

        FileWriter::iterable($filename, $iterable());

        self::assertFileExists($filename);
        self::assertSame([], \glob($filename . '.*'));
        self::assertSame("foo\nbar\nbaz\n", (string)\file_get_contents($filename));
    }

    #[Test]
    public function iterableOverwritesExpectedFile(): void
    {
        $old_contents = "qux\nquux\nquuz\n";
        $iterable = static function (): \Generator {
            yield 'foo' . \PHP_EOL;
            yield 'bar' . \PHP_EOL;
            yield 'baz' . \PHP_EOL;
        };

        $filename = __DIR__ . '/test.txt';
        \file_put_contents($filename, $old_contents);
        self::assertFileExists($filename);
        self::assertSame($old_contents, (string)\file_get_contents($filename));

        FileWriter::iterable($filename, $iterable());

        self::assertFileExists($filename);
        self::assertSame([], \glob($filename . '.*'));
        self::assertSame("foo\nbar\nbaz\n", (string)\file_get_contents($filename));
    }
}
