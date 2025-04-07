<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto;

enum PasetoPurpose: string
{
    case Local = 'local';
    case Public = 'public';

    public const string LOCAL = 'local';
    public const string PUBLIC = 'public';
}
