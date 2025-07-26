<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App;

use PhoneBurner\SaltLite\Enum\WithStringBackedInstanceStaticMethod;

enum BuildStage: string
{
    use WithStringBackedInstanceStaticMethod;

    case Production = 'production';
    case Integration = 'integration';
    case Development = 'development';
}
