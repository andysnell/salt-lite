<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Attribute;

use PhoneBurner\SaltLite\Cryptography\Attribute\HashAlgorithmProperties;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(HashAlgorithmProperties::class)]
final class HashAlgorithmPropertiesTest extends TestCase
{
    #[Test]
    public function constructsWithOnlyRequiredArguments(): void
    {
        $digest_bytes = 32;
        $properties = new HashAlgorithmProperties($digest_bytes);

        self::assertInstanceOf(HashAlgorithmProperties::class, $properties);
        self::assertSame($digest_bytes, $properties->digest_bytes);
        self::assertFalse($properties->cryptographic);
        self::assertFalse($properties->broken);
    }

    #[Test]
    public function constructsWithAllArguments(): void
    {
        $digest_bytes = 64;
        $cryptographic = true;
        $broken = true;

        $properties = new HashAlgorithmProperties(
            digest_bytes: $digest_bytes,
            cryptographic: $cryptographic,
            broken: $broken,
        );

        self::assertInstanceOf(HashAlgorithmProperties::class, $properties);
        self::assertSame($digest_bytes, $properties->digest_bytes);
        self::assertTrue($properties->cryptographic);
        self::assertTrue($properties->broken);
    }
}
