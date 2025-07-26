<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Serialization;

use PhoneBurner\SaltLite\Enum\WithUnitEnumInstanceStaticMethod;

enum Serializer
{
    use WithUnitEnumInstanceStaticMethod;

    case Igbinary;
    case Php;
}
