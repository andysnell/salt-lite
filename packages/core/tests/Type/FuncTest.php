<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Helper;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Time\TimeZone\Tz;
use PhoneBurner\SaltLite\Type\Func;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FuncTest extends TestCase
{
    /**
     * @param array<mixed> $args
     */
    #[DataProvider('providesCallableValuesWithArgs')]
    #[Test]
    public function valueCallsCallableValues(callable $test, array $args, mixed $expected): void
    {
        self::assertSame($expected, Func::value($test, ...$args));
    }

    #[DataProvider('providesCallableValuesWithoutArgs')]
    #[Test]
    public function valueCallsCallableValuesWithoutArgs(callable $test, mixed $expected): void
    {
        self::assertSame($expected, Func::value($test));
    }

    #[Test]
    public function fwdReturnsCallableThatCallsMethodOnObjectWithoutArgs(): void
    {
        $epoch = CarbonImmutable::createFromTimestamp(0);
        $dates = \array_map(static fn(int $i): CarbonImmutable => $epoch->addMinutes($i), [0, 1, 2, 3]);

        self::assertSame([0, 60, 120, 180,], \array_map(Func::fwd('getTimestamp'), $dates));
    }

    #[Test]
    public function fwdReturnsCallableThatCallsMethodOnObjectWithSingleArgs(): void
    {
        $epoch = CarbonImmutable::createFromTimestamp(1, Tz::Utc->value);
        $dates = \array_map(static fn(int $i): CarbonImmutable => $epoch->addDays($i), [0, 1, 2, 3]);

        self::assertSame([
            '1970-01-01',
            '1970-01-02',
            '1970-01-03',
            '1970-01-04',
        ], \array_map(Func::fwd('format', 'Y-m-d'), $dates));
    }

    #[Test]
    public function fwdReturnsCallableThatCallsMethodOnObjectWithMultipleArgs(): void
    {
        $arr = \array_map(static fn(int $i): object => self::makeMockObject($i), [0, 1, 2, 3]);

        self::assertSame([
            0,
            2 * 3 * 5,
            2 * 2 * 3 * 5,
            3 * 2 * 3 * 5,
        ], \array_map(Func::fwd('foo', 2, 3, 5), $arr));
    }

    #[Test]
    public function noopReturnsFunctionThatDoesNothing(): void
    {
        $func = Func::noop();
        $test = $func();

        self::assertNull($test);

        $reflection = new \ReflectionFunction($func);
        $type = $reflection->getReturnType();
        self::assertInstanceOf(\ReflectionNamedType::class, $type);
        self::assertSame('void', $type->getName());
    }

    private static function makeMockObject(int $i): object
    {
        return new readonly class ($i) {
            public function __construct(private int $i)
            {
            }

            public function foo(int $j, int $k, int $l): int
            {
                return $this->i * $j * $k * $l;
            }
        };
    }

    public static function providesCallableValuesWithoutArgs(): \Generator
    {
        yield [static fn(): int => 123, 123];

        $class = new class () {
            public function __invoke(): int
            {
                return 999;
            }
        };

        yield [$class, 999];
    }

    public static function providesCallableValuesWithArgs(): \Generator
    {
        yield [static fn(): int => 123, [], 123];
        yield [static fn(): int => 123, [22, 10], 123];
        yield [static fn($i): int|float => 123 + $i, [22, 10], 145];
        yield [static fn($i, $j): int|float => 123 + $i + $j, [22, 10], 155];

        $class = new class () {
            public function __invoke(int $i = 0, int $j = 0): int
            {
                return 123 + $i + $j;
            }
        };

        yield [$class, [], 123];
        yield [$class, [22], 145];
        yield [$class, [22, 10], 155];

        yield ['trim', ['  Hello, World  '], 'Hello, World'];
    }

    /**
     * @param array<mixed> $args
     */
    #[DataProvider('providesNonCallableValues')]
    #[Test]
    public function valuePassesThroughNonCallableValues(mixed $test, array $args): void
    {
        self::assertSame($test, Func::value($test, ...$args));
    }

    public static function providesNonCallableValues(): \Generator
    {
        $values = [
            true,
            false,
            null,
            'string',
            12343,
            123.33,
            new \stdClass(),
        ];

        foreach ($values as $value) {
            yield [$value, []];
        }

        foreach ($values as $value) {
            yield [$value, [23, 23, 23]];
        }
    }
}
