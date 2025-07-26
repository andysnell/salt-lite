<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\App;

use PhoneBurner\SaltLite\App\BuildStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BuildStageTest extends TestCase
{
    #[Test]
    #[DataProvider('buildStageValuesProvider')]
    public function enumHasExpectedValues(string $name, string $value): void
    {
        $case = BuildStage::from($value);
        self::assertSame($value, $case->value);
        self::assertSame($name, $case->name);
    }

    #[Test]
    #[DataProvider('buildStageValuesProvider')]
    public function instanceCreatesEnumFromString(string $name, string $value): void
    {
        $case = BuildStage::instance($value);
        self::assertSame($value, $case->value);
        self::assertSame($name, $case->name);
    }

    #[Test]
    public function instanceIsCaseInsensitive(): void
    {
        $case = BuildStage::instance('PRODUCTION');
        self::assertSame('production', $case->value);
        self::assertSame('Production', $case->name);
    }

    #[Test]
    public function instanceThrowsExceptionForInvalidValue(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        BuildStage::instance('invalid');
    }

    #[Test]
    public function castReturnsEnumForValidValue(): void
    {
        $result = BuildStage::cast('production');
        self::assertNotNull($result);
        self::assertSame('production', $result->value);
        self::assertSame('Production', $result->name);
    }

    #[Test]
    public function castReturnsNullForInvalidValue(): void
    {
        $result = BuildStage::cast('invalid');
        self::assertNull($result);
    }

    public static function buildStageValuesProvider(): \Iterator
    {
        yield 'production' => ['Production', 'production'];
        yield 'integration' => ['Integration', 'integration'];
        yield 'development' => ['Development', 'development'];
    }
}
