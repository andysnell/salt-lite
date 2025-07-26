<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Exception;

class InvalidStringableOffset extends \InvalidArgumentException
{
    public function __construct(mixed $offset)
    {
        parent::__construct(\sprintf(
            'Invalid offset type: %s. Expected a stringable type.',
            \get_debug_type($offset),
        ));
    }
}
