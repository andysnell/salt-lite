<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class TooManyRequestsResponse extends GenericHttpExceptionResponse
{
    protected int $status_code = HttpStatus::TOO_MANY_REQUESTS;
    protected string $title = HttpReasonPhrase::TOO_MANY_REQUESTS;
}
