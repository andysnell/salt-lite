<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ParameterOverride;

enum OverrideType
{
    case Position;
    case Name;
    case Hint;
}
