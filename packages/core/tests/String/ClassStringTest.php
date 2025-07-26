<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\String;

use PhoneBurner\SaltLite\Cryptography\KeyManagement\Key;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringProhibitsSerialization;
use PhoneBurner\SaltLite\String\ClassString\ClassString;
use PhoneBurner\SaltLite\String\ClassString\ClassStringType;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ClassStringTest extends TestCase
{
    #[Test]
    public function happyPathTestEnum(): void
    {
        $sut = new ClassString(Encoding::class);
        self::assertSame(Encoding::class, (string)$sut);
        self::assertSame(Encoding::class, $sut->value);
        self::assertSame(ClassStringType::Enum, $sut->type);
        self::assertTrue($sut->is(Encoding::Base64));
        self::assertTrue($sut->is(Encoding::class));
        self::assertFalse($sut->is(ClassStringType::class));
        self::assertFalse($sut->is(ClassStringType::Enum));
        self::assertSame(Encoding::class, $sut->reflect()->getName());
        self::assertEquals($sut, \unserialize(\serialize($sut)));
    }

    #[Test]
    public function happyPathTestInterface(): void
    {
        $sut = new ClassString(Key::class);
        self::assertSame(Key::class, (string)$sut);
        self::assertSame(Key::class, $sut->value);
        self::assertSame(ClassStringType::Interface, $sut->type);
        self::assertTrue($sut->is(Key::class));
        self::assertTrue($sut->is(BinaryString::class));
        self::assertFalse($sut->is(SharedKey::generate()));
        self::assertFalse($sut->is(SharedKey::class));
        self::assertSame(Key::class, $sut->reflect()->getName());
        self::assertEquals($sut, \unserialize(\serialize($sut)));
    }

    #[Test]
    public function happyPathTestClass(): void
    {
        $sut = new ClassString(SharedKey::class);
        self::assertSame(SharedKey::class, (string)$sut);
        self::assertSame(SharedKey::class, $sut->value);
        self::assertSame(ClassStringType::Object, $sut->type);
        self::assertTrue($sut->is(SharedKey::class));
        self::assertTrue($sut->is(Key::class));
        self::assertTrue($sut->is(BinaryString::class));
        self::assertTrue($sut->is(SharedKey::generate()));
        self::assertFalse($sut->is(ClassStringType::class));
        self::assertFalse($sut->is(ClassStringType::Enum));
        self::assertSame(SharedKey::class, $sut->reflect()->getName());
        self::assertEquals($sut, \unserialize(\serialize($sut)));

        self::assertEquals($sut, ClassString::match(SharedKey::class, SharedKey::class));
        self::assertEquals($sut, ClassString::match(SharedKey::class, Key::class));
        self::assertEquals($sut, ClassString::match(SharedKey::class, BinaryString::class));
    }

    #[Test]
    public function sadPathMatch(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage(\sprintf("Class '%s' does not match type '%s'", Key::class, SharedKey::class));
        ClassString::match(Key::class, SharedKey::class);
    }

    #[Test]
    public function sadPathString(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Class Foo does not exist");
        new ClassString('Foo');
    }

    #[Test]
    public function sadPathTrait(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage("Traits are not supported");
        new ClassString(BinaryStringProhibitsSerialization::class);
    }
}
