<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Enum;

use PhoneBurner\SaltLite\Enum\EnumCaseAttr;
use PhoneBurner\SaltLite\Exception\NotInstantiable;
use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockClassConstantAttribute;
use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockEnumAttribute;
use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockRepeatableEnumAttribute;
use PhoneBurner\SaltLite\Tests\Fixtures\EnumWithAttributes;
use PhoneBurner\SaltLite\Tests\Fixtures\EnumWithMultipleAttributes;
use PhoneBurner\SaltLite\Tests\Fixtures\EnumWithoutAttributes;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(EnumCaseAttr::class)]
final class EnumCaseAttrTest extends TestCase
{
    #[Test]
    public function classIsNotInstantiable(): void
    {
        $this->expectException(NotInstantiable::class);
        $this->expectExceptionMessage(
            \sprintf('Class %s is not instantiable', EnumCaseAttr::class),
        );
        new EnumCaseAttr();
    }

    #[Test]
    #[DataProvider('findCasesProvider')]
    public function findReturnsExpectedAttributes(
        \BackedEnum $enum_case,
        string|null $attribute_class,
        array $expected_attributes,
    ): void {
        self::assertTrue($attribute_class === null || \class_exists($attribute_class));
        $actual = EnumCaseAttr::find($enum_case, $attribute_class);
        self::assertEquals($expected_attributes, $actual);
    }

    public static function findCasesProvider(): \Generator
    {
        yield 'no attributes defined, no specific class' => [
            EnumWithoutAttributes::CaseA,
            null,
            [],
        ];

        yield 'no attributes defined, specific class' => [
            EnumWithoutAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            [],
        ];

        yield 'one attribute defined, no specific class' => [
            EnumWithAttributes::CaseA,
            null,
            [new MockRepeatableEnumAttribute('Case A Value')],
        ];

        yield 'one attribute defined, matching specific class' => [
            EnumWithAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            [new MockRepeatableEnumAttribute('Case A Value')],
        ];

        yield 'one attribute defined, non-matching specific class' => [
            EnumWithAttributes::CaseA,
            MockClassConstantAttribute::class,
            [],
        ];

        $expected_multiple = [
            new MockRepeatableEnumAttribute('Multi A 1'),
            new MockClassConstantAttribute('Multi A 2'),
            new MockRepeatableEnumAttribute('Multi A 3'),
        ];
        yield 'multiple attributes defined, no specific class' => [
            EnumWithMultipleAttributes::CaseA,
            null,
            $expected_multiple,
        ];

        yield 'multiple attributes defined, matching specific class (TestAttribute)' => [
            EnumWithMultipleAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            [
                new MockRepeatableEnumAttribute('Multi A 1'),
                new MockRepeatableEnumAttribute('Multi A 3'),
            ],
        ];

        yield 'multiple attributes defined, matching specific class (TestAttributeB)' => [
            EnumWithMultipleAttributes::CaseA,
            MockClassConstantAttribute::class,
            [new MockClassConstantAttribute('Multi A 2')],
        ];

        yield 'multiple attributes defined, non-matching specific class (TestAttributeC)' => [
            EnumWithMultipleAttributes::CaseA,
            MockEnumAttribute::class,
            [],
        ];
    }

    #[Test]
    #[DataProvider('firstCasesProvider')]
    public function firstReturnsExpectedAttributeOrNull(
        \BackedEnum $enum_case,
        string|null $attribute_class,
        object|null $expected_attribute,
    ): void {
        self::assertTrue($attribute_class === null || \class_exists($attribute_class));
        $actual = EnumCaseAttr::first($enum_case, $attribute_class);
        self::assertEquals($expected_attribute, $actual);
    }

    public static function firstCasesProvider(): \Generator
    {
        yield 'no attributes defined, no specific class' => [
            EnumWithoutAttributes::CaseA,
            null,
            null,
        ];

        yield 'no attributes defined, specific class' => [
            EnumWithoutAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            null,
        ];

        yield 'one attribute defined, no specific class' => [
            EnumWithAttributes::CaseA,
            null,
            new MockRepeatableEnumAttribute('Case A Value'),
        ];

        yield 'one attribute defined, matching specific class' => [
            EnumWithAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            new MockRepeatableEnumAttribute('Case A Value'),
        ];

        yield 'one attribute defined, non-matching specific class' => [
            EnumWithAttributes::CaseA,
            MockClassConstantAttribute::class,
            null,
        ];

        yield 'multiple attributes defined, no specific class' => [
            EnumWithMultipleAttributes::CaseA,
            null,
            new MockRepeatableEnumAttribute('Multi A 1'),
        ];

        yield 'multiple attributes defined, matching specific class (TestAttribute)' => [
            EnumWithMultipleAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            new MockRepeatableEnumAttribute('Multi A 1'),
        ];

        yield 'multiple attributes defined, matching specific class (TestAttributeB)' => [
            EnumWithMultipleAttributes::CaseA,
            MockClassConstantAttribute::class,
            new MockClassConstantAttribute('Multi A 2'),
        ];

        yield 'multiple attributes defined, non-matching specific class (TestAttributeC)' => [
            EnumWithMultipleAttributes::CaseA,
            MockEnumAttribute::class,
            null,
        ];
    }

    #[Test]
    #[DataProvider('fetchSuccessCasesProvider')]
    public function fetchReturnsExpectedAttribute(
        \BackedEnum $enum_case,
        string $attribute_class,
        object $expected_attribute,
    ): void {
        self::assertTrue(\class_exists($attribute_class));
        $actual = EnumCaseAttr::fetch($enum_case, $attribute_class);
        self::assertEquals($expected_attribute, $actual);
    }

    public static function fetchSuccessCasesProvider(): \Generator
    {
        yield 'one attribute defined, matching specific class' => [
            EnumWithAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            new MockRepeatableEnumAttribute('Case A Value'),
        ];

        yield 'multiple attributes defined, matching specific class (TestAttribute)' => [
            EnumWithMultipleAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            new MockRepeatableEnumAttribute('Multi A 1'),
        ];

        yield 'multiple attributes defined, matching specific class (TestAttributeB)' => [
            EnumWithMultipleAttributes::CaseA,
            MockClassConstantAttribute::class,
            new MockClassConstantAttribute('Multi A 2'),
        ];
    }

    #[Test]
    #[DataProvider('fetchThrowsExceptionCasesProvider')]
    public function fetchThrowsExceptionWhenAttributeNotFound(
        \BackedEnum $enum_case,
        string $attribute_class,
        string $expected_exception_message,
    ): void {
        $this->expectException(\LogicException::class); // Changed from RuntimeException
        $this->expectExceptionMessage($expected_exception_message);
        /** @phpstan-ignore argument.type, argument.templateType */
        EnumCaseAttr::fetch($enum_case, $attribute_class);
    }

    public static function fetchThrowsExceptionCasesProvider(): \Generator
    {
        yield 'no attributes defined, specific class' => [
            EnumWithoutAttributes::CaseA,
            MockRepeatableEnumAttribute::class,
            \sprintf("Attribute %s Not Found for Enum Case %s::CaseA", MockRepeatableEnumAttribute::class, EnumWithoutAttributes::class), // Updated message
        ];

        yield 'one attribute defined, non-matching specific class' => [
            EnumWithAttributes::CaseA,
            MockClassConstantAttribute::class,
           \sprintf("Attribute %s Not Found for Enum Case %s::CaseA", MockClassConstantAttribute::class, EnumWithAttributes::class), // Updated message
        ];

        yield 'multiple attributes defined, non-matching specific class (TestAttributeC)' => [
            EnumWithMultipleAttributes::CaseA,
            MockEnumAttribute::class,
            \sprintf("Attribute %s Not Found for Enum Case %s::CaseA", MockEnumAttribute::class, EnumWithMultipleAttributes::class), // Updated message
        ];
    }
}
