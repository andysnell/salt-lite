<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Filesystem;

use PhoneBurner\SaltLite\Filesystem\Exception\UnableToReadFile;
use PhoneBurner\SaltLite\Filesystem\Exception\UnableToWriteFile;
use PhoneBurner\SaltLite\Filesystem\FileMode;
use PhoneBurner\SaltLite\Filesystem\FileStream;
use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;
use PhoneBurner\SaltLite\Type\Type;
use Psr\Http\Message\StreamInterface;

final readonly class File
{
    use HasNonInstantiableBehavior;

    public static function read(\Stringable|string $filename): string
    {
        return @\file_get_contents(self::filename($filename))
            ?: throw UnableToReadFile::atLocation((string)$filename);
    }

    public static function write(\Stringable|string $filename, string $content): int
    {
        return \file_put_contents(self::filename($filename), $content)
            ?: throw UnableToWriteFile::atLocation((string)$filename);
    }

    public static function stream(\Stringable|string $filename, FileMode $mode = FileMode::Read): FileStream|null
    {
        try {
            return new FileStream(self::filename($filename), $mode);
        } catch (\Exception) {
            return null;
        }
    }

    /**
     * @return resource stream
     */
    public static function open(
        \Stringable|string $filename,
        FileMode $mode = FileMode::Read,
        mixed $context = null,
    ): mixed {
        $context = match (true) {
            $context === null => null,
            \is_resource($context) && \get_resource_type($context) === 'stream-context' => $context,
            default => throw new \InvalidArgumentException('context must be null or stream-context resource'),
        };

        $stream = @\fopen(self::filename($filename), $mode->value, false, $context);
        if ($stream === false) {
            throw new \RuntimeException('Could Not Create Stream');
        }

        return $stream;
    }

    public static function size(mixed $value): int
    {
        return match (true) {
            Type::isStreamResource($value) => \fstat($value)['size'] ?? null,
            $value instanceof StreamInterface => $value->getSize() ?? 0,
            $value instanceof \SplFileInfo => $value->getSize() ?: 0,
            \is_string($value) && \file_exists($value) => \filesize($value) ?: 0,
            default => throw new \InvalidArgumentException('Unsupported Type:' . \get_debug_type($value)),
        } ?? throw new \RuntimeException('Unable to Get Size of Stream');
    }

    public static function close(mixed $value): void
    {
        match (true) {
            Type::isStreamResource($value) => \fclose($value),
            $value instanceof StreamInterface => $value->close(),
            default => null,
        };
    }

    private static function filename(\Stringable|string $filename): string
    {
        return match (true) {
            \is_string($filename) => $filename,
            $filename instanceof \SplFileInfo => $filename->getPathname(),
            default => (string)$filename,
        };
    }
}
