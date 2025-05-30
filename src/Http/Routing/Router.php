<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;

interface Router
{
    public function resolveForRequest(ServerRequestInterface $request): RouterResult;

    public function resolveByName(string $name): RouterResult;
}
