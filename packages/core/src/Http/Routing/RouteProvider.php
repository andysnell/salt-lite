<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing;

use PhoneBurner\SaltLite\Http\Routing\Definition\Definition;

interface RouteProvider
{
    /**
     * @return array<Definition>
     */
    public function __invoke(): array;
}
