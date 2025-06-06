<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Uuid;

use Generator;
use PhoneBurner\SaltLite\Uuid\Uuid;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Rfc4122\NilUuid;
use Ramsey\Uuid\UuidInterface;
use stdClass;
use Stringable;
use Throwable;

final class UuidTest extends TestCase
{
    #[Test]
    public function randomReturnsVersion4UuidInstances(): void
    {
        $uuids = [];
        for ($i = 0; $i < 100; ++$i) {
            $uuid = Uuid::random();
            self::assertMatchesRegularExpression(Uuid::HEX_REGEX, (string)$uuid);
            $fields = $uuid->getFields();
            self::assertInstanceOf(FieldsInterface::class, $fields);
            self::assertSame(2, $fields->getVariant());
            self::assertSame(4, $fields->getVersion());
            $uuids[(string)$uuid] = $uuid;
        }

        self::assertCount(100, $uuids);
    }

    #[Test]
    public function nilReturnsTheNilUuidInstance(): void
    {
        $uuid = Uuid::nil();
        self::assertInstanceOf(NilUuid::class, $uuid);
        self::assertSame('00000000-0000-0000-0000-000000000000', $uuid->toString());
        self::assertSame("\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0", $uuid->getBytes());
        $fields = $uuid->getFields();
        self::assertInstanceOf(FieldsInterface::class, $fields);
        self::assertTrue($fields->isNil());
        self::assertNull($fields->getVersion());
        self::assertSame($uuid, Uuid::nil());
    }

    #[Test]
    public function orderedReturnsTimestampFirstCombUuidInstances(): void
    {
        $uuid = Uuid::ordered();
        $reduced_comparison = 0;
        for ($i = 0; $i < 100; ++$i) {
            $new_uuid = Uuid::ordered();
            self::assertMatchesRegularExpression(Uuid::HEX_REGEX, (string)$new_uuid);
            self::assertLessThan($new_uuid->toString(), $uuid->toString());
            self::assertLessThan($new_uuid->getBytes(), $uuid->getBytes());
            $fields = $uuid->getFields();
            self::assertInstanceOf(FieldsInterface::class, $fields);
            self::assertSame(2, $fields->getVariant());
            self::assertSame(7, $fields->getVersion());
            $reduced_comparison += $new_uuid->compareTo($uuid);
            $uuid = $new_uuid;
        }

        self::assertSame(100, $reduced_comparison);
    }

    public function fromStringReturnsMatchingUuid(): void
    {
        $uuid = Uuid::random();
        self::assertTrue($uuid->equals(
            Uuid::instance($uuid->toString()),
        ));
    }

    #[Test]
    public function instanceReturnsSameUuidInterfaceInstance(): void
    {
        $uuid = Uuid::random();
        self::assertSame($uuid, Uuid::instance($uuid));
    }

    #[Test]
    public function instanceCastsStringsToUuidInterface(): void
    {
        $uuid = Uuid::random();

        $uuid_upper_string = \strtoupper($uuid->toString());
        $uuid_lower_string = \strtolower($uuid->toString());
        $uuid_stringable = new readonly class ($uuid) implements Stringable {
            public function __construct(private UuidInterface $uuid)
            {
            }

            public function __toString(): string
            {
                return (string)$this->uuid;
            }
        };

        self::assertTrue($uuid->equals(Uuid::instance($uuid_upper_string)));
        self::assertTrue($uuid->equals(Uuid::instance($uuid_lower_string)));
        self::assertTrue($uuid->equals(Uuid::instance($uuid_stringable)));
    }

    #[DataProvider('provideUncastableUuidValues')]
    #[Test]
    public function instanceThrowsExceptionIfCannotCastToUuidInterface(mixed $value): void
    {
        $this->expectException(Throwable::class);
        /** @phpstan-ignore argument.type (intentional defect) */
        Uuid::instance($value);
    }

    public static function provideUncastableUuidValues(): Generator
    {
        yield [''];
        yield [new stdClass()];
        yield [1234567890];
        yield ['Z0000000-0000-0000-0000-000000000000'];
    }
}
