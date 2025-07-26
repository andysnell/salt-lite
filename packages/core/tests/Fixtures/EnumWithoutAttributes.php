<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

enum EnumWithoutAttributes: string
{
    case CaseA = 'a';
    case CaseB = 'b';
}
