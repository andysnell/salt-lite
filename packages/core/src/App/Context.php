<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App;

enum Context
{
    case Http;
    case Cli;
    case Test;
}
