<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockRepeatableEnumAttribute;

enum EnumWithAttributes: string
{
    #[MockRepeatableEnumAttribute('Case A Value')]
    case CaseA = 'a';

    #[MockRepeatableEnumAttribute('Case B Value')]
    case CaseB = 'b';
}
