<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockClassConstantAttribute;
use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockRepeatableEnumAttribute;

enum EnumWithMultipleAttributes: string
{
    #[MockRepeatableEnumAttribute('Multi A 1')]
    #[MockClassConstantAttribute('Multi A 2')]
    #[MockRepeatableEnumAttribute('Multi A 3')]
    case CaseA = 'a';

    #[MockClassConstantAttribute('Multi B 1')]
    #[MockRepeatableEnumAttribute('Multi B 2')]
    case CaseB = 'b';
}
