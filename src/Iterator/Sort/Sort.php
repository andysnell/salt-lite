<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator\Sort;

/**
 * @template TKey of array-key
 * @template TValue
 */
class Sort
{
    /**
     * Sorts an array or iterable that can be cast to an array by value,
     * without preserving any index association. That is, the values
     * will be returned as a zero-indexed list. The original argument
     * will not be altered; however, if it was an iterator, it will be
     * consumed.
     *
     * @param iterable<TKey, TValue> $iterable
     * @param Order|(callable(TValue $a, TValue $b): int) $order Either an instance
     * of Order or a callback returns an integer less than, equal to, or greater
     * than zero if the first argument is less than, equal to, or greater than
     * the second, respectively.
     * @return list<TValue>
     */
    public static function list(
        iterable $iterable,
        Order|callable $order = Order::Ascending,
        Comparison $type = Comparison::Regular,
    ): array {
        if (! \is_array($iterable)) {
            $iterable = \iterator_to_array($iterable, false);
        }

        $iterable !== [] && match ($order) {
            Order::Ascending => \sort($iterable, $type->value),
            Order::Descending => \rsort($iterable, $type->value),
            default => \usort($iterable, $order),
        };

        /** @var list<TValue> $iterable */
        return $iterable;
    }

    /**
     * @param iterable<TKey, TValue> $iterable
     * @param Order|(callable(TValue $a, TValue $b): int) $order Either an instance
     * of Order or a callback returns an integer less than, equal to, or greater
     * than zero if the first argument is less than, equal to, or greater than
     * the second, respectively.
     * @return array<TKey, TValue>
     */
    public static function associative(
        iterable $iterable,
        Order|callable $order = Order::Ascending,
        Comparison $type = Comparison::Regular,
    ): array {
        if (! \is_array($iterable)) {
            $iterable = \iterator_to_array($iterable, true);
        }

        $iterable !== [] && match ($order) {
            Order::Ascending => \asort($iterable, $type->value),
            Order::Descending => \arsort($iterable, $type->value),
            default => \uasort($iterable, $order),
        };

        return $iterable;
    }

    /**
     * @param iterable<TKey, TValue> $iterable
     * @param Order|(callable(TKey $a, TKey $b): int) $order Either an instance
     * of Order or a callback returns an integer less than, equal to, or greater
     * than zero if the first argument is less than, equal to, or greater than
     * the second, respectively.
     * @return array<TKey, TValue>
     */
    public static function key(
        iterable $iterable,
        Order|callable $order = Order::Ascending,
        Comparison $type = Comparison::Regular,
    ): array {
        if (! \is_array($iterable)) {
            $iterable = \iterator_to_array($iterable, true);
        }

        $iterable !== [] && match ($order) {
            Order::Ascending => \ksort($iterable, $type->value),
            Order::Descending => \krsort($iterable, $type->value),
            default => \uksort($iterable, $order),
        };

        return $iterable;
    }
}
