<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

enum IntBackedEnum: int
{
    case Foo = 1;
    case Bar = 2;
    case Baz = 3;
}
