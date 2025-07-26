<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Uuid;

use PhoneBurner\SaltLite\Uuid\Uuid;
use PhoneBurner\SaltLite\Uuid\UuidString;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class UuidStringTest extends TestCase
{
    #[Test]
    public function itIsAUUID(): void
    {
        $uuid = Uuid::ordered();
        $wrapped_uuid = new UuidString($uuid);

        self::assertTrue($wrapped_uuid->equals($uuid));
        self::assertSame(0, $wrapped_uuid->compareTo($uuid));
        self::assertSame(0, $uuid->compareTo($wrapped_uuid));
        self::assertTrue($wrapped_uuid->equals($uuid));
        self::assertTrue($wrapped_uuid->equals($wrapped_uuid));
        self::assertTrue($uuid->equals($wrapped_uuid));
        self::assertSame($uuid->getBytes(), $wrapped_uuid->getBytes());
        self::assertEquals($uuid->getFields(), $wrapped_uuid->getFields());
        self::assertEquals($uuid->getHex(), $wrapped_uuid->getHex());
        self::assertEquals($uuid->getInteger(), $wrapped_uuid->getInteger());
        self::assertSame($uuid->toString(), $wrapped_uuid->toString());
        self::assertSame((string)$uuid, (string)$wrapped_uuid);

        $deserialized = \unserialize(\serialize($wrapped_uuid));
        self::assertInstanceOf(UuidString::class, $deserialized);
        self::assertSame($wrapped_uuid->toString(), $deserialized->toString());
        self::assertTrue($deserialized->equals($uuid));
        self::assertSame(0, $deserialized->compareTo($uuid));
        self::assertSame(0, $uuid->compareTo($deserialized));
        self::assertTrue($deserialized->equals($uuid));
        self::assertTrue($deserialized->equals($wrapped_uuid));
        self::assertTrue($uuid->equals($deserialized));
        self::assertSame($uuid->getBytes(), $deserialized->getBytes());
        self::assertEquals($uuid->getFields(), $deserialized->getFields());
        self::assertEquals($uuid->getHex(), $deserialized->getHex());
        self::assertEquals($uuid->getInteger(), $deserialized->getInteger());
        self::assertSame($uuid->toString(), $deserialized->toString());
        self::assertSame((string)$uuid, (string)$deserialized);
    }

    #[Test]
    public function itCanBeConstructedFromString(): void
    {
        $uuid_string = Uuid::random()->toString();
        $uuid_obj = new UuidString($uuid_string);

        self::assertSame($uuid_string, $uuid_obj->toString());
        self::assertSame($uuid_string, (string)$uuid_obj);
    }

    #[Test]
    public function itCanBeConstructedFromStringable(): void
    {
        $uuid = Uuid::random();
        $uuid_string = $uuid->toString();

        $stringable = new readonly class ($uuid_string) implements \Stringable {
            public function __construct(private string $uuid)
            {
            }

            public function __toString(): string
            {
                return $this->uuid;
            }
        };

        $uuid_obj = new UuidString($stringable);

        self::assertSame($uuid_string, $uuid_obj->toString());
        self::assertSame($uuid_string, (string)$uuid_obj);
    }

    #[Test]
    public function itCanBeJsonSerialized(): void
    {
        $uuid = Uuid::random();
        $uuid_string = $uuid->toString();
        $uuid_obj = new UuidString($uuid_string);

        self::assertSame($uuid_string, $uuid_obj->jsonSerialize());
        self::assertSame(\sprintf('"%s"', $uuid_string), \json_encode($uuid_obj));
    }

    #[Test]
    public function itReturnsUrn(): void
    {
        $uuid = Uuid::random();
        $uuid_obj = new UuidString($uuid);

        self::assertSame('urn:uuid:' . $uuid->toString(), $uuid_obj->getUrn());
    }

    #[Test]
    public function itProperlyDelegatesDeprecatedMethods(): void
    {
        $uuid = Uuid::random();
        $uuid_obj = new UuidString($uuid);

        /** @phpstan-ignore method.deprecated, method.deprecated */
        self::assertSame($uuid->getFieldsHex(), $uuid_obj->getFieldsHex());
        /** @phpstan-ignore method.deprecated, method.deprecated */
        self::assertSame($uuid->getClockSequenceHex(), $uuid_obj->getClockSequenceHex());
        /** @phpstan-ignore method.deprecated, method.deprecated */
        self::assertSame($uuid->getNodeHex(), $uuid_obj->getNodeHex());
        /** @phpstan-ignore method.deprecated, method.deprecated */
        self::assertSame($uuid->getVariant(), $uuid_obj->getVariant());
        /** @phpstan-ignore method.deprecated, method.deprecated */
        self::assertSame($uuid->getVersion(), $uuid_obj->getVersion());

        // Test the timestamp-related methods that are only applicable for version 1 UUIDs
        $uuid_obj = new UuidString('376cca1c-14c4-11f0-aa82-ca307efc5917');
        /** @phpstan-ignore method.deprecated */
        self::assertSame('1f014c4376cca1c', $uuid_obj->getTimestampHex());
        /** @phpstan-ignore method.deprecated */
        self::assertEquals(new \DateTimeImmutable('2025-04-08 21:55:42.450742 +00:00'), $uuid_obj->getDateTime());
    }
}
