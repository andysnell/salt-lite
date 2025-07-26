<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Enum\WithStringBackedInstanceStaticMethod;
use PhoneBurner\SaltLite\Enum\WithValuesStaticMethod;

enum StoplightState: string
{
    use WithStringBackedInstanceStaticMethod;
    use WithValuesStaticMethod;

    case Red = 'red';
    case Yellow = 'yellow';
    case Green = 'green';
}
