<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class UnavailableForLegalReasonsResponse extends GenericHttpExceptionResponse
{
    protected int $status_code = HttpStatus::UNAVAILABLE_FOR_LEGAL_REASONS;
    protected string $title = HttpReasonPhrase::UNAVAILABLE_FOR_LEGAL_REASONS;
    protected string $detail = 'Access to the resource is prohibited as a consequence of a legal demand, requirement, or action.';
}
