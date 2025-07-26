<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests;

use PhoneBurner\SaltLite\Tests\Fixtures\FinalClassWithPublicProperty;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function PhoneBurner\SaltLite\ghost;
use function PhoneBurner\SaltLite\null_if_false;
use function PhoneBurner\SaltLite\proxy;

final class FunctionTest extends TestCase
{
    #[Test]
    public function proxyInitializesObjectWhenAccessed(): void
    {
        $test_class = new FinalClassWithPublicProperty();
        $test_class->property = 'initialized';

        $factory = static fn(FinalClassWithPublicProperty $object): FinalClassWithPublicProperty => $test_class;
        $proxy = proxy($factory);

        self::assertInstanceOf(FinalClassWithPublicProperty::class, $proxy);
        self::assertNotSame($test_class, $proxy);
        self::assertSame('initialized', $proxy->property);
    }

    #[Test]
    public function ghostCreatesLazyGhostObject(): void
    {
        $initializer = static function (FinalClassWithPublicProperty $object): void {
            $object->property = 'value';
        };

        $ghost = ghost($initializer);

        self::assertInstanceOf(FinalClassWithPublicProperty::class, $ghost);
        self::assertSame('value', $ghost->property);
    }

    #[Test]
    #[DataProvider('nullIfFalseDataProvider')]
    public function nullIfFalseReturnsExpectedValue(mixed $input, mixed $expected): void
    {
        self::assertSame($expected, null_if_false($input));
    }

    public static function nullIfFalseDataProvider(): \Generator
    {
        $object = new \stdClass();
        yield 'false returns null' => [false, null];
        yield 'null returns null' => [null, null];
        yield 'string returns string' => ['value', 'value'];
        yield 'integer returns integer' => [123, 123];
        yield 'empty array returns empty array' => [[], []];
        yield 'zero returns zero' => [0, 0];
        yield 'empty string returns empty string' => ['', ''];
        yield 'zero float returns zero float' => [0.0, 0.0];
        yield 'object returns same object' => [$object, $object];
    }
}
