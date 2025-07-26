<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Type;

use Generator;
use PhoneBurner\SaltLite\String\Str;
use PhoneBurner\SaltLite\Tests\Fixtures\AbsorbsLightWaves;
use PhoneBurner\SaltLite\Tests\Fixtures\Mirror;
use PhoneBurner\SaltLite\Tests\Fixtures\PropertyFixture;
use PhoneBurner\SaltLite\Tests\Fixtures\ReflectsLightWaves;
use PhoneBurner\SaltLite\Tests\Fixtures\ShinyThing;
use PhoneBurner\SaltLite\Type\Reflect;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;

final class ReflectTest extends TestCase
{
    #[Test]
    public function objectReturnsReflectionClassForFullyQualifiedClassname(): void
    {
        self::assertEquals(new ReflectionClass(Mirror::class), Reflect::object(Mirror::class));
    }

    #[Test]
    public function objectReturnsReflectionClassForObjectInstance(): void
    {
        $mirror = new Mirror();
        self::assertEquals(new ReflectionClass($mirror::class), Reflect::object($mirror));
    }

    #[Test]
    public function methodReturnsReflectionMethodForFullyQualifiedClassnameAndMethod(): void
    {
        $expected = new ReflectionMethod(Mirror::class, 'getBar');
        self::assertEquals($expected, Reflect::method(Mirror::class, 'getBar'));
    }

    #[Test]
    public function methodReturnsReflectionMethodForObjectInstanceAndMethod(): void
    {
        $mirror = new Mirror();
        $expected = new ReflectionMethod($mirror, 'getBar');
        self::assertEquals($expected, Reflect::method($mirror, 'getBar'));
    }

    #[Test]
    public function setPropertySetsNonpublicPropertyAndReturnsObject(): void
    {
        $mirror = new Mirror();
        $reflection = Reflect::setProperty($mirror, 'foo', 'bazqux');
        self::assertSame($mirror, $reflection);
        self::assertSame('bazqux', $mirror->getFoo());
    }

    #[Test]
    public function getPropertyReturnsValueOfNonpublicProperty(): void
    {
        self::assertSame(7654321, Reflect::getProperty(new Mirror(), 'bar'));
    }

    #[Test]
    public function getConstantsReturnsAllClassConstantsForFullyQualifiedClassname(): void
    {
        self::assertSame([
            'RED' => 1,
            'BLUE' => 2,
            'GREEN' => 3,
            'YELLOW' => 'this is protected',
            'PURPLE' => 'this is private',
        ], Reflect::getConstants(Mirror::class));
    }

    #[Test]
    public function getConstantsReturnsAllClassConstantsForObjectInstance(): void
    {
        self::assertSame([
            'RED' => 1,
            'BLUE' => 2,
            'GREEN' => 3,
            'YELLOW' => 'this is protected',
            'PURPLE' => 'this is private',
        ], Reflect::getConstants(new Mirror()));
    }

    #[Test]
    public function getPublicConstantsReturnsPublicClassConstantsForFullyQualifiedClassname(): void
    {
        self::assertSame([
            'RED' => 1,
            'BLUE' => 2,
            'GREEN' => 3,
        ], Reflect::getPublicConstants(Mirror::class));
    }

    #[Test]
    public function getPublicConstantsReturnsPublicClassConstantsForObjectInstance(): void
    {
        self::assertSame([
            'RED' => 1,
            'BLUE' => 2,
            'GREEN' => 3,
        ], Reflect::getPublicConstants(new Mirror()));
    }

    /**
     * @param object|class-string $class_or_object
     * @param class-string $interface
     */
    #[DataProvider('providesInvalidInterfaceStringTestCases')]
    #[Test]
    public function implementsThrowsExceptionWhenPassedBadInterface(
        object|string $class_or_object,
        string $interface,
    ): void {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage($interface . ' is not a valid and defined interface');
        Reflect::implements($class_or_object, $interface);
    }

    /**
     * @return Generator<array{object|class-string, class-string}>
     */
    public static function providesInvalidInterfaceStringTestCases(): Generator
    {
        $interfaces = [
            'parent_class' => ShinyThing::class,
            'self_class' => Mirror::class,
            'invalid_interface' => '\Networx\Tests\Unit\Salt\Common\Helper\Fixture\ReflectsSoundWaves',
        ];

        $class_or_objects = [
            'object_with_' => new Mirror(),
            'class_with_' => Mirror::class,
        ];

        foreach ($class_or_objects as $key => $class_or_object) {
            foreach ($interfaces as $name => $interface) {
                /** @phpstan-ignore generator.valueType */
                yield $key . $name => [$class_or_object, $interface];
            }
        }
    }

    #[TestWith([true, ReflectsLightWaves::class])]
    #[TestWith([false, AbsorbsLightWaves::class])]
    #[Test]
    public function implementsReturnsTrueIfObjectImplementsInterface(bool $expected, string $interface): void
    {
        self::assertSame($expected, Reflect::implements(new Mirror(), $interface));
    }

    #[TestWith([true, ReflectsLightWaves::class])]
    #[TestWith([false,AbsorbsLightWaves::class])]
    #[Test]
    public function implementsReturnsTrueIfClassImplementsInterface(bool $expected, string $interface): void
    {
        self::assertSame($expected, Reflect::implements(Mirror::class, $interface));
    }

    /**
     * @param object|class-string $class
     */
    #[DataProvider('providesInvalidClassOrObjectTestCases')]
    #[Test]
    public function implementReturnsFalseIfPassedInvalidClassOrObject(mixed $class): void
    {
        self::assertFalse(Reflect::implements($class, ReflectsLightWaves::class));
    }

    /**
     * @return Generator<array<mixed>>
     */
    public static function providesInvalidClassOrObjectTestCases(): Generator
    {
        yield 'null' => [null];
        yield 'true' => [true];
        yield 'false' => [false];
        yield 'zero' => [0];
        yield 'int' => [1];
        yield 'float' => [1.2];
        yield 'empty_array' => [[]];
        yield 'array' => [['foo' => 'bar', 'baz' => 'quz']];
        yield 'resource' => [Str::stream('Hello, World')->detach()];
        yield 'class_does_not_exist' => ['\Networx\Tests\Unit\Salt\Common\Helper\Fixture\InvisibleMirror'];
    }

    /**
     * @param object|class-string $class_or_object
     */
    #[DataProvider('providesHasPropertyTestCases')]
    #[Test]
    public function hasPropertyReturnsTrueIfClassOrObjectHasProperty(
        object|string $class_or_object,
        string $property,
        bool $expected,
    ): void {
        self::assertSame($expected, Reflect::hasProperty($class_or_object, $property));
    }

    /**
     * @return Generator<array{object|class-string, string, bool}>
     */
    public static function providesHasPropertyTestCases(): Generator
    {
        $properties = [
            'public_property',
            'protected_property',
            'private_property',
            'string_property',
            'iterable_property',
            'concrete_property',
            'nullable_string_property',
            'nullable_iterable_property',
            'nullable_concrete_property',
        ];

        $class_or_objects = [
            'object_with_' => new PropertyFixture(),
            'class_with_' => PropertyFixture::class,
        ];

        foreach ($class_or_objects as $key => $class_or_object) {
            foreach ($properties as $property) {
                yield $key . $property => [$class_or_object, $property, true];
            }
            yield $key . 'not_defined_property' => [$class_or_object, 'not_defined_property', false];
        }
    }
}
