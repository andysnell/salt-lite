<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Configuration\Struct;

use PhoneBurner\SaltLite\Configuration\ConfigStruct;

/**
 * @phpstan-require-implements ConfigStruct
 */
trait ConfigStructSerialization
{
    public function __serialize(): array
    {
        return \get_object_vars($this);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function __unserialize(array $data): void
    {
        $this->__construct(...$data);
    }
}
