<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Routing\Domain;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Routing\Domain\StaticFile;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StaticFileTest extends TestCase
{
    #[Test]
    public function happyPathIsHappy(): void
    {
        $path = '/path/to/file';
        $content_type = ContentType::TEXT;

        $static_file = new StaticFile($path, $content_type);

        self::assertSame($path, $static_file->path);
        self::assertSame($content_type, $static_file->content_type);
    }
}
