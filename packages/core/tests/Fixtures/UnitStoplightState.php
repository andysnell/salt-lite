<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Enum\WithUnitEnumInstanceStaticMethod;

enum UnitStoplightState
{
    use WithUnitEnumInstanceStaticMethod;

    case Red;
    case Yellow;
    case Green;
}
