<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Configuration;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use Psr\Container\ContainerInterface;

#[Contract]
interface Configuration extends ContainerInterface
{
    /**
     * Gets a configuration value by key (dot notation),
     * returning null if no value is set.
     */
    public function get(string $id): mixed;
}
