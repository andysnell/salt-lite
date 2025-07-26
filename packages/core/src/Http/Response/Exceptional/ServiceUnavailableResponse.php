<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class ServiceUnavailableResponse extends GenericHttpExceptionResponse
{
    protected int $status_code = HttpStatus::SERVICE_UNAVAILABLE;
    protected string $title = HttpReasonPhrase::SERVICE_UNAVAILABLE;
}
