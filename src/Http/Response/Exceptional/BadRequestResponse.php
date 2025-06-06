<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class BadRequestResponse extends GenericHttpExceptionResponse
{
    protected int $status_code = HttpStatus::BAD_REQUEST;
    protected string $title = HttpReasonPhrase::BAD_REQUEST;
    protected string $detail = 'The request could not be understood by the server due to malformed syntax or invalid content.';
}
