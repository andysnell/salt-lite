<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Enum;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

/**
 * Helper for functions specific to resolving and working with attributes defined
 * on enum cases.
 */
final readonly class EnumCaseAttr
{
    use HasNonInstantiableBehavior;

    /**
     * Find attributes on any object, class-string, or reflection instance that
     * supports the "getAttributes" method. Passing a class-string as the
     * $attribute_name will filter the results to only include attributes of
     * that type.
     *
     * @template T of object
     * @param class-string<T>|null $attribute_name
     * @return ($attribute_name is null ? list<object> : array<T>)
     */
    public static function find(
        \UnitEnum $enum_case,
        string|null $attribute_name = null,
        bool $use_instanceof = false,
    ): array {
        return \array_map(
            static fn(\ReflectionAttribute $reflection_attribute): object => $reflection_attribute->newInstance(),
            new \ReflectionEnumUnitCase($enum_case::class, $enum_case->name)->getAttributes($attribute_name, $use_instanceof ? \ReflectionAttribute::IS_INSTANCEOF : 0),
        );
    }

    /**
     * Find the first attribute on a enum case instance that
     * supports the "getAttributes" method. Passing a class-string as the
     * $attribute_name will filter the results to only include attributes of
     * that type.
     *
     * @template T of object
     * @param class-string<T>|null $attribute_name
     * @return ($attribute_name is null ? object : T)
     */
    public static function first(
        \UnitEnum $enum_case,
        string|null $attribute_name = null,
        bool $use_instanceof = false,
    ): object|null {
        return self::find($enum_case, $attribute_name, $use_instanceof)[0] ?? null;
    }

    /**
     * Fetch a new instance of the first defined attribute of a given type
     * on an enum case, throwing an exception on failure.
     *
     * @template T of object
     * @param class-string<T> $attribute_name
     * @return T&object
     */
    public static function fetch(
        \UnitEnum $enum_case,
        /** @var class-string<T> $attribute_name */
        string $attribute_name,
        bool $use_instanceof = false,
    ): object {
        return self::first($enum_case, $attribute_name, $use_instanceof)
            ?? throw new \LogicException(\vsprintf('Attribute %s Not Found for Enum Case %s::%s', [
                $attribute_name,
                $enum_case::class,
                $enum_case->name,
            ]));
    }
}
