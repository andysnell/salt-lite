<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Random;

/**
 * Represents an item with an associated weight for weighted random selection.
 *
 * @template-covariant T
 */
final readonly class WeightedItem
{
    /**
     * @param T $value
     * @param non-negative-int $weight
     * @see \PhoneBurner\SaltLite\Random\Randomizer::weighted() for documentation
     * on how to use this class and the meaning of the `weight` parameter.
     */
    public function __construct(
        public mixed $value,
        public int $weight,
    ) {
        $this->weight >= 0 || throw new \UnexpectedValueException('weight must be a positive integer.');
    }
}
