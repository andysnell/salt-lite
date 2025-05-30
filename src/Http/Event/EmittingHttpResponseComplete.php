<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Event;

use Psr\Http\Message\ResponseInterface;

final readonly class EmittingHttpResponseComplete
{
    public function __construct(public ResponseInterface $request)
    {
    }
}
