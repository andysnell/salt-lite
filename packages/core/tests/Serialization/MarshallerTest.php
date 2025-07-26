<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Serialization;

use PhoneBurner\SaltLite\Domain\Memory\Bytes;
use PhoneBurner\SaltLite\Serialization\Exception\SerializationFailure;
use PhoneBurner\SaltLite\Serialization\Marshaller;
use PhoneBurner\SaltLite\Serialization\Serializer;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MarshallerTest extends TestCase
{
    #[Test]
    public function serializeAndDeserializeCommonValues(): void
    {
        self::assertNull(Marshaller::deserialize(Marshaller::serialize(null)));
        self::assertTrue(Marshaller::deserialize(Marshaller::serialize(true)));
        self::assertFalse(Marshaller::deserialize(Marshaller::serialize(false)));
        self::assertSame([], Marshaller::deserialize(Marshaller::serialize([])));
        self::assertSame(42, Marshaller::deserialize(Marshaller::serialize(42)));
        self::assertSame(3.14, Marshaller::deserialize(Marshaller::serialize(3.14)));
        self::assertSame('test', Marshaller::deserialize(Marshaller::serialize('test')));
    }

    #[Test]
    public function serializeAndDeserializeComplexValues(): void
    {
        $array = ['nested' => ['value' => 42]];
        self::assertSame($array, Marshaller::deserialize(Marshaller::serialize($array)));

        $object = new \stdClass();
        $object->property = 'value';
        self::assertEquals($object, Marshaller::deserialize(Marshaller::serialize($object)));
    }

    #[Test]
    public function serializeWithEncoding(): void
    {
        $serialized = Marshaller::serialize('test', Encoding::Base64, true);
        self::assertStringStartsWith(Encoding::BASE64_PREFIX, $serialized);
        self::assertSame('test', Marshaller::deserialize($serialized));
    }

    #[Test]
    public function compression(): void
    {
        $large_string = \str_repeat('test', 1000);
        $serialized = Marshaller::serialize($large_string, null, false, true);
        self::assertStringStartsWith("\x78", $serialized);
        self::assertSame($large_string, Marshaller::deserialize($serialized));
    }

    #[Test]
    public function compressionWithCustomThreshold(): void
    {
        $string = \str_repeat('test', 10);
        $serialized = Marshaller::serialize($string, null, false, true, new Bytes(10));
        self::assertStringStartsWith("\x78", $serialized);
        self::assertSame($string, Marshaller::deserialize($serialized));
    }

    #[Test]
    public function serializerSelection(): void
    {
        if (\extension_loaded('igbinary')) {
            $serialized = Marshaller::serialize('test', null, false, false, new Bytes(1000), Serializer::Igbinary);
            self::assertStringStartsWith("\x00\x00\x00\x02", $serialized);
        } else {
            $serialized = Marshaller::serialize('test', null, false, false, new Bytes(1000), Serializer::Php);
            self::assertStringStartsWith('s:', $serialized);
        }
    }

    #[Test]
    public function errorCases(): void
    {
        $this->expectException(SerializationFailure::class);
        $this->expectExceptionMessage('cannot serialize resource');
        Marshaller::serialize(\fopen('php://memory', 'r'));
    }

    #[Test]
    public function invalidSerializedData(): void
    {
        $this->expectException(SerializationFailure::class);
        $this->expectExceptionMessage('unsupported serialization format');
        Marshaller::deserialize('invalid:data');
    }

    #[Test]
    public function edgeCases(): void
    {
        self::assertSame('', Marshaller::deserialize(Marshaller::serialize('')));
        self::assertSame([], Marshaller::deserialize(Marshaller::serialize([])));
        self::assertSame(0, Marshaller::deserialize(Marshaller::serialize(0)));
    }
}
