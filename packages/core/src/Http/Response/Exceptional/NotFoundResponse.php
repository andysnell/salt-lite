<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class NotFoundResponse extends GenericHttpExceptionResponse
{
    protected int $status_code = HttpStatus::NOT_FOUND;
    protected string $title = HttpReasonPhrase::NOT_FOUND;
    protected string|null $http_reason_phrase = HttpReasonPhrase::NOT_FOUND;
}
