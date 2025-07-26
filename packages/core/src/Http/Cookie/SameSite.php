<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Cookie;

enum SameSite
{
    case Lax;
    case Strict;
    case None;
}
