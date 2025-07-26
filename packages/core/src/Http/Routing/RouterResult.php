<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing;

use PhoneBurner\SaltLite\Http\Routing\Match\RouteMatch;

interface RouterResult
{
    public function isFound(): bool;

    public function getRouteMatch(): RouteMatch;
}
