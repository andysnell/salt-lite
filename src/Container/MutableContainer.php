<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use Psr\Container\ContainerInterface;

/**
 * @template T
 */
#[Contract]
interface MutableContainer extends ContainerInterface
{
    public function has(\Stringable|string $id): bool;

    /**
     * @return T
     */
    public function get(\Stringable|string $id): mixed;

    /**
     * @param T $value
     */
    public function set(\Stringable|string $id, mixed $value): void;

    public function unset(\Stringable|string $id): void;
}
