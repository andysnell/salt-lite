<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Iterator;

use PhoneBurner\SaltLite\Iterator\Arrayable;
use PhoneBurner\SaltLite\Iterator\Iter;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Iter::class)]
final class IterTest extends TestCase
{
    /**
     * @param Arrayable<array-key, mixed>|iterable<mixed> $input
     * @param array<mixed> $array
     */
    #[DataProvider('providesArrayAndIteratorTestCases')]
    #[Test]
    public function iteratorReturnsAnIteratorFromIterable(mixed $input, array $array): void
    {
        $converted = Iter::cast($input);
        self::assertInstanceOf(\Iterator::class, $converted);
        self::assertSame($array, \iterator_to_array($converted));
        if ($input instanceof \Iterator) {
            self::assertSame($input, $converted);
        }
    }

    /**
     * @return \Generator<array>
     */
    public static function providesArrayAndIteratorTestCases(): \Generator
    {
        $test_arrays = [
            'empty' => [],
            'simple' => [1, 2, 3],
            'associative' => ['foo' => 1, 'bar' => 2, 'baz' => 3],
            'non-sequential' => [0 => 'foo', 42 => 'bar', 23 => 'baz'],
        ];

        $test = static fn($input, array $array): array => ['input' => $input, 'array' => $array];
        foreach ($test_arrays as $type => $array) {
            yield 'array_' . $type => $test($array, $array);
            yield 'generator_' . $type => $test((static fn() => yield from $array)(), $array);
            yield 'iterator_' . $type => $test(new \ArrayIterator($array), $array);
            yield 'iterator_aggregate' . $type => $test(self::makeIteratorAggregate($array), $array);
            yield 'arrayable_' . $type => $test(self::makeArrayable($array), $array);
        }
    }

    /**
     * @param array<mixed> $array
     * @return \IteratorAggregate<mixed>
     */
    public static function makeIteratorAggregate(array $array): \IteratorAggregate
    {
        return new readonly class ($array) implements \IteratorAggregate {
            /**
             * @param array<mixed> $array
             */
            public function __construct(private array $array)
            {
            }

            /**
             * @return \Generator<mixed>
             */
            public function getIterator(): \Generator
            {
                yield from $this->array;
            }
        };
    }

    /**
     * @param array<mixed> $arrayable_array
     * @param array<mixed> $iterator_array
     * @return Arrayable<array-key, mixed>&\IteratorAggregate<mixed>
     */
    public static function makeIterableArrayable(array $arrayable_array, array $iterator_array): object
    {
        return new readonly class ($arrayable_array, $iterator_array) implements Arrayable, \IteratorAggregate {
            /**
             * @param array<mixed> $arrayable_array
             * @param array<mixed> $iterator_array
             */
            public function __construct(private array $arrayable_array, private array $iterator_array)
            {
            }

            /**
             * @return array<mixed>
             */
            public function toArray(): array
            {
                return $this->arrayable_array;
            }

            /**
             * @return \Generator<mixed>
             */
            public function getIterator(): \Generator
            {
                yield from $this->iterator_array;
            }
        };
    }

    /**
     * @param array<mixed> $array
     * @return Arrayable<array-key, mixed>
     */
    public static function makeArrayable(array $array): Arrayable
    {
        return new readonly class ($array) implements Arrayable {
            /**
             * @param array<mixed> $array
             */
            public function __construct(private array $array)
            {
            }

            /**
             * @return array<mixed>
             */
            public function toArray(): array
            {
                return $this->array;
            }
        };
    }

    #[Test]
    #[DataProvider('providesFirstLastTestCases')]
    public function firstAndLastReturnsExpectedElements(iterable $input, mixed $first, mixed $last): void
    {
        self::assertSame($first, Iter::first($input));
        self::assertSame($last, Iter::last($input));
    }

    public static function providesFirstLastTestCases(): \Generator
    {
        yield 'empty array' => [[], null, null];
        yield 'simple array' => [[1, 2, 3], 1, 3];
        yield 'single_value_array' => [['apple'], 'apple', 'apple'];
        yield 'associative array' => [['a' => 'apple', 'b' => 'banana'], 'apple', 'banana'];
        yield 'generator' => [(static fn() => yield from [10, 20, 30])(), 10, 30];
        yield 'iterator' => [new \ArrayIterator(['x', 'y', 'z']), 'x', 'z'];
    }

    #[Test]
    public function mapAppliesCallbackAndYieldsResults(): void
    {
        $input = ['a' => 1, 'b' => 2, 'c' => 3];
        $callback = static fn(int $value, int|string $key): string => $key . '=' . ($value * 2);
        $expected = ['a' => 'a=2', 'b' => 'b=4', 'c' => 'c=6'];

        $generator = Iter::map($callback, $input);
        self::assertInstanceOf(\Generator::class, $generator);
        self::assertSame($expected, \iterator_to_array($generator));
    }

    #[Test]
    public function amapAppliesCallbackAndReturnsArray(): void
    {
        $input = new \ArrayIterator([1, 2, 3]);
        $callback = static fn(int $value): int => $value * $value;
        $expected = [0 => 1, 1 => 4, 2 => 9]; // Note: ArrayIterator preserves original keys

        $result = Iter::amap($callback, $input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function chainCombinesMultipleIterables(): void
    {
        $array1 = [1, 2];
        $iter3 = new \ArrayIterator([3, 4]);
        $array4 = [5, 6];

        // iterators can repeat keys
        $chained = Iter::chain($array1, $iter3, $array4);
        $counter = 0;
        foreach ($chained as $key => $value) {
            self::assertIsScalar($key);
            self::assertIsScalar($value);
            match (++$counter) {
                /** @phpstan-ignore match.alwaysFalse (this is a weird comparison, but it's valid) */
                1 => self::assertSame([0, 1], [$key, $value]),
                2 => self::assertSame([1, 2], [$key, $value]),
                3 => self::assertSame([0, 3], [$key, $value]),
                4 => self::assertSame([1, 4], [$key, $value]),
                5 => self::assertSame([0, 5], [$key, $value]),
                6 => self::assertSame([1, 6], [$key, $value]),
                default => throw new \Exception('Unexpected value: ' . $key . ' => ' . $value),
            };
        }
    }

    #[Test]
    public function chainWithSingleIterable(): void
    {
        $array = [1, 2, 3];
        $chained = Iter::chain($array);
        self::assertSame($array, \iterator_to_array($chained));
    }

    #[Test]
    public function chainWithEmptyIterable(): void
    {
        $chained = Iter::chain([]);
        self::assertSame([], \iterator_to_array($chained));
    }

    #[Test]
    public function chainWithNoArguments(): void
    {
        $chained = Iter::chain();
        self::assertSame([], \iterator_to_array($chained));
    }
}
