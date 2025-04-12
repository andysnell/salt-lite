<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Type;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;
use ReflectionClass;
use ReflectionMethod;

final readonly class Reflect
{
    use HasNonInstantiableBehavior;

    /**
     * This method is mostly obsoleted after PHP 8.4 by chaining methods off of
     * `new ReflectionClass()` directly; however, it is still useful due to the
     *  generic type hinting, which works with both `object` and `class-string`.
     *
     * @template T of object
     * @param T|class-string<T> $class_or_object
     * @return ReflectionClass<T>
     */
    public static function object(object|string $class_or_object): ReflectionClass
    {
        return \is_object($class_or_object)
            ? new ReflectionClass($class_or_object::class)
            : new ReflectionClass($class_or_object);
    }

    /**
     * @param object|class-string $class_or_object
     */
    public static function method(object|string $class_or_object, string $method): ReflectionMethod
    {
        return new ReflectionMethod($class_or_object, $method);
    }

    /**
     * @param object|class-string $class_or_object
     */
    public static function hasProperty(object|string $class_or_object, string $property): bool
    {
        return new ReflectionClass($class_or_object)->hasProperty($property);
    }

    /**
     * @template T of object
     * @param T $object
     * @return T
     */
    public static function setProperty(object $object, string $property, mixed $value = null): object
    {
        new ReflectionClass($object)->getProperty($property)->setValue($object, $value);
        return $object;
    }

    public static function getProperty(object $object, string $property): mixed
    {
        return new ReflectionClass($object)->getProperty($property)->getValue($object);
    }

    /**
     * @param object|class-string $class_or_object
     * @return mixed|false Value of the constant with the name $name or `false`
     *    if the constant was not found in the class.
     */
    public static function getConstant(object|string $class_or_object, string $name): mixed
    {
        return new ReflectionClass($class_or_object)->getConstant($name);
    }

    /**
     * @param object|class-string $class_or_object
     * @return array<string,mixed>
     */
    public static function getConstants(object|string $class_or_object): array
    {
        return new ReflectionClass($class_or_object)->getConstants();
    }

    /**
     * @param object|class-string $class_or_object
     * @return array<string,mixed>
     */
    public static function getPublicConstants(object|string $class_or_object): array
    {
        return new ReflectionClass($class_or_object)->getConstants(\ReflectionClassConstant::IS_PUBLIC);
    }

    /**
     * Does the class or object passed as `$class_or_object` implement the interface
     * referred to by the `$interface` string? This method does essentially the
     * same thing as the PHP builtin function 'is_a' with the extra conditional
     * checks to ensure that the interface and subject class both exist.
     * Note: This method does not work to check if a class inherits from another.
     */
    public static function implements(mixed $class_or_object, string $interface): bool
    {
        if (! \interface_exists($interface)) {
            throw new \InvalidArgumentException($interface . ' is not a valid and defined interface');
        }

        if ($class_or_object instanceof $interface) {
            return true;
        }

        return \is_string($class_or_object)
            && \class_exists($class_or_object)
            && \is_a($class_or_object, $interface, true);
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @param callable(T): void $initializer
     * @return T&object
     */
    public static function ghost(
        string $class,
        callable $initializer,
        bool $skip_initialization_on_serialize = false,
    ): object {
        $flags = 0;
        $flags |= $skip_initialization_on_serialize ? ReflectionClass::SKIP_INITIALIZATION_ON_SERIALIZE : 0;

        $ghost = new ReflectionClass($class)->newLazyGhost($initializer, $flags);
        \assert($ghost instanceof $class);

        return $ghost;
    }

    /**
     * This method wraps the passed factory in another closure that will make sure
     * that the object ultimately returned is not a lazy object. This is useful
     * when we do not necessarily know if the factory is going to give us something
     * that is lazy or not, e.g. If the factory is just resolving the class from
     * the PSR-11 container, for something like a database connection or entity,
     * the object returned by the container may be another ghost or proxy.
     *
     * @template T of object
     * @param class-string<T> $class
     * @param callable(T): T $factory
     * @return T
     */
    public static function proxy(
        string $class,
        callable $factory,
        bool $skip_initialization_on_serialize = false,
    ): object {
        $options = 0;
        $options |= $skip_initialization_on_serialize ? ReflectionClass::SKIP_INITIALIZATION_ON_SERIALIZE : 0;

        $proxy = new ReflectionClass($class)->newLazyProxy(
            /** @phpstan-ignore argument.type */
            static fn(object $object): object => new ReflectionClass($class)->initializeLazyObject($factory($object)),
            $options,
        );
        \assert($proxy instanceof $class);

        return $proxy;
    }

    /**
     * Resolve the reflection for the case of an instance of an enum.
     *
     * Note that we resolve the case through \ReflectionEnum, instead of \ReflectionEnumUnitCase
     * directly so that the return value is \ReflectionEnumBackedCase when the
     * enum is backed.
     *
     * @return ($enum is \BackedEnum ? \ReflectionEnumBackedCase : \ReflectionEnumUnitCase)
     */
    public static function case(\UnitEnum $enum): \ReflectionEnumUnitCase
    {
        return new \ReflectionEnum($enum)->getCase($enum->name);
    }
}
