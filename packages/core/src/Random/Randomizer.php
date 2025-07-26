<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Random;

use Random\IntervalBoundary;
use UnitEnum as T;

/**
 * Dependency-injection-friendly random number generator that should allow for easy mocking in tests,
 * as the PHP \Random\Randomizer class is a final, internal class that cannot be
 * mocked directly.
 */
class Randomizer
{
    public const string NUMERIC_CHARS = '0123456789';
    public const string UPPER_ALPHA_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public const string LOWER_ALPHA_CHARS = 'abcdefghijklmnopqrstuvwxyz';
    public const string ALPHA_CHARS = self::UPPER_ALPHA_CHARS . self::LOWER_ALPHA_CHARS;
    public const string ALPHANUMERIC_CHARS = self::NUMERIC_CHARS . self::ALPHA_CHARS;
    public const string HEX_CHARS = '0123456789abcdef';
    public const string SPECIAL_CHARS = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    public function __construct(
        private readonly \Random\Randomizer $randomizer = new \Random\Randomizer(),
    ) {
    }

    public function bytes(int $bytes): string
    {
        $bytes > 0 || throw new \UnexpectedValueException('bytes must be greater than 0');
        return $this->randomizer->getBytes($bytes);
    }

    public function hex(int $bytes): string
    {
        $bytes > 0 || throw new \UnexpectedValueException('bytes must be greater than 0');
        return \bin2hex($this->randomizer->getBytes($bytes));
    }

    public function chars(int $length, string $chars = self::ALPHANUMERIC_CHARS): string
    {
        return match (true) {
            $length <= 0 => throw new \UnexpectedValueException('length must be greater than 0'),
            $chars === '' => throw new \UnexpectedValueException('chars string must not be empty'),
            default => $this->randomizer->getBytesFromString($chars, $length),
        };
    }

    public function int(int $min = \PHP_INT_MIN, int $max = \PHP_INT_MAX): int
    {
        $min <= $max || throw new \UnexpectedValueException('min must be less than or equal to max');
        return $this->randomizer->getInt($min, $max);
    }

    public function float(
        float $min = 0.0,
        float $max = 1.0,
        IntervalBoundary $boundary = IntervalBoundary::ClosedOpen,
    ): float {
        $min <= $max || throw new \UnexpectedValueException('min must be less than or equal to max');
        return $this->randomizer->getFloat($min, $max, $boundary);
    }

    /**
     * @template T
     * @param string|array<T> $value
     * @return ($value is string ? string : array<T>)
     */
    public function shuffle(string|array $value): string|array
    {
        return \is_string($value)
            ? $this->randomizer->shuffleBytes($value)
            : $this->randomizer->shuffleArray($value);
    }

    /**
     * @template T of int|string
     * @param array<T, mixed> $array
     * @return array<T>
     */
    public function keys(array $array, int $num, bool $shuffle = true): array
    {
        return match (true) {
            $num === 0, $array === [] => [],
            $num === 1 => $this->randomizer->pickArrayKeys($array, 1),
            $shuffle => $this->randomizer->shuffleArray(
                $this->randomizer->pickArrayKeys($array, \min($num, \count($array))),
            ),
            default => $this->randomizer->pickArrayKeys($array, \min($num, \count($array))),
        };
    }

    /**
     * @template T of int|string
     * @param array<T, mixed> $array
     * @return T|null
     */
    public function key(array $array): int|string|null
    {
        return $array === [] ? null : $this->randomizer->pickArrayKeys($array, 1)[0];
    }

    /**
     * @template T
     * @param array<array-key, T> $array
     * @return T|null
     */
    public function value(array $array): mixed
    {
        return $array === [] ? null : $array[$this->key($array)];
    }

    /**
     * Note: the array keys can be preserved in the returned array depending on
     * the value of the $preserve_keys argument.
     *
     * @template T
     * @param array<array-key, T> $array
     * @return ($preserve_keys is true ? array<array-key, T> : list<T>)
     */
    public function values(array $array, int $num, bool $preserve_keys = true): array
    {
        $values = [];
        foreach ($this->keys($array, $num) as $key) {
            $values[$key] = $array[$key];
        }
        return $preserve_keys ? $values : \array_values($values);
    }

    /**
     * @template T of \UnitEnum
     * @param (T&\UnitEnum)|class-string<T&\UnitEnum> $enum_class
     */
    public function enum(\UnitEnum|string $enum_class): \UnitEnum
    {
        if (! \is_a($enum_class, \UnitEnum::class, true)) {
            throw new \InvalidArgumentException(\sprintf('Class %s is not a UnitEnum', $enum_class));
        }

        return $this->value($enum_class::cases()) ?? throw new \LogicException('Enum has no cases');
    }

    /**
     * Selects a random item from an array of WeightedItem instances based on their weights.
     * The probability of selecting an item is proportional to its weight and
     * the total weight of all items.
     *
     * For example, if you have three items each with the weight of 1 (i.e. [1, 1, 1]),
     * the probability of selecting each item is 1/3. If you have three items with
     * weights of [1, 2, 3], the probability of selecting the first item is 1/6,
     * the second item is 2/6 (or 1/3), and the third item is 3/6 (or 1/2). Items
     * with a weight of 0 will never be selected, so the total weight must be
     * at least 1 for the method to work correctly. Instead of working with floats,
     * increase the scale of the weights to avoid floating-point precision issues,
     * e.g., use weights of [10, 20, 30] instead of [1.0, 2.0, 3.0]. Assuming
     * the random number generator is fair, every integer in the range has
     * an equal chance of being selected. Thus, there is no difference between
     * weights of [1, 1, 0], [50, 50, 0], and [1000, 1000, 0] to represent a 50%
     * chance of selecting between the first two items and never selecting the
     * third item.
     *
     * @template T
     * @param WeightedItem<T> ...$items An array of WeightedItem instances.
     * @return T The selected item from the WeightedItem.
     */
    public function weighted(WeightedItem ...$items): mixed
    {
        $total_weight = \array_sum(\array_column($items, 'weight')) ?: throw new \UnexpectedValueException(
            'At least one weighted item is required total weight of items must be greater than 0',
        );

        $random_value = $this->randomizer->getInt(1, $total_weight);
        foreach ($items as $item) {
            $random_value -= $item->weight;
            if ($random_value <= 0) {
                return $item->value;
            }
        }

        throw new \LogicException('No item selected, this should never happen');
    }
}
