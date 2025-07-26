<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Uuid;

use PhoneBurner\SaltLite\Uuid\OrderedUuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\FieldsInterface;
use Ramsey\Uuid\Uuid;

final class OrderedUuidTest extends TestCase
{
    #[Test]
    public function itIsAUUID(): void
    {
        $uuid = new OrderedUuid();

        self::assertTrue(Uuid::isValid((string)$uuid));
        $fields = $uuid->getFields();
        self::assertInstanceOf(FieldsInterface::class, $fields);
        self::assertSame(Uuid::UUID_TYPE_UNIX_TIME, $fields->getVersion());

        $fields = Uuid::fromString((string)$uuid)->getFields();
        self::assertInstanceOf(FieldsInterface::class, $fields);
        self::assertSame(Uuid::UUID_TYPE_UNIX_TIME, $fields->getVersion());
    }
}
