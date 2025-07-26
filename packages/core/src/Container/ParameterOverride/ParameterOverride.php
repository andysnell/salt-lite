<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ParameterOverride;

interface ParameterOverride
{
    public function type(): OverrideType;

    public function identifier(): string|int;

    public function value(): mixed;
}
