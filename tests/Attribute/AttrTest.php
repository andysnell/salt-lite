<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use PhoneBurner\SaltLite\Attribute\Attr;
use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockAttribute;
use PhoneBurner\SaltLite\Tests\Fixtures\ClassWithAttributes;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class AttrTest extends TestCase
{
    #[Test]
    public function findReturnsAttributesOnClass(): void
    {
        $attributes = Attr::find(ClassWithAttributes::class, MockAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(MockAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function findReturnsAttributesOnReflectionClass(): void
    {
        $reflection = new ReflectionClass(ClassWithAttributes::class);
        $attributes = Attr::find($reflection, MockAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(MockAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function findReturnsAttributesOnReflectionMethod(): void
    {
        $reflection = new ReflectionMethod(ClassWithAttributes::class, 'method');
        $attributes = Attr::find($reflection, MockAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(MockAttribute::class, $attributes[0]);
        self::assertSame('method', $attributes[0]->name);
    }

    #[Test]
    public function findReturnsAttributesOnReflectionProperty(): void
    {
        $reflection = new ReflectionProperty(ClassWithAttributes::class, 'property');
        $attributes = Attr::find($reflection, MockAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(MockAttribute::class, $attributes[0]);
        self::assertSame('property', $attributes[0]->name);
    }

    #[Test]
    public function findReturnsAttributesOnObject(): void
    {
        $object = new ClassWithAttributes();
        $attributes = Attr::find($object, MockAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(MockAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function findReturnsEmptyArrayWhenNoAttributesFound(): void
    {
        $attributes = Attr::find(\stdClass::class, MockAttribute::class);
        self::assertCount(0, $attributes);
    }

    #[Test]
    public function firstReturnsFirstAttribute(): void
    {
        $attribute = Attr::first(ClassWithAttributes::class, MockAttribute::class);
        self::assertInstanceOf(MockAttribute::class, $attribute);
        self::assertSame('class', $attribute->name);
    }

    #[Test]
    public function firstReturnsNullWhenNoAttributesFound(): void
    {
        $attribute = Attr::first(\stdClass::class, MockAttribute::class);
        self::assertNull($attribute);
    }

    #[Test]
    #[DataProvider('invalidReflectorProvider')]
    public function findThrowsExceptionForInvalidReflector(mixed $invalid_reflector): void
    {
        $this->expectException(\UnexpectedValueException::class);
        /** @phpstan-ignore argument.type (intentional defect) */
        Attr::find($invalid_reflector);
    }

    public static function invalidReflectorProvider(): \Iterator
    {
        $reflector = new class implements \Reflector {
            public function __toString(): string
            {
                return 'Invalid reflector';
            }

            public static function export(): null
            {
                // TODO: compatible with < PHP 8.0 definitions
                return null;
            }
        };

        yield 'custom reflector' => [$reflector];
    }
}
