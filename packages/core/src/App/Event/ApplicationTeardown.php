<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App\Event;

use PhoneBurner\SaltLite\App\App;

final readonly class ApplicationTeardown
{
    public function __construct(public App $app)
    {
    }
}
