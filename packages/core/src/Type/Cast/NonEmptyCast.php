<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Type\Cast;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class NonEmptyCast
{
    use HasNonInstantiableBehavior;

    /**
     * @phpstan-assert non-empty-string $value
     * @return non-empty-string
     */
    public static function string(string $value, \Exception|null $exception = null): string
    {
        if ($value === '') {
            throw $exception ?? new \UnexpectedValueException('String Must Not Be Empty');
        }

        return $value;
    }
}
