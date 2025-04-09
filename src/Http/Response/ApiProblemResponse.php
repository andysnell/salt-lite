<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response;

use Crell\ApiProblem\ApiProblem;
use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;

class ApiProblemResponse extends JsonResponse
{
    public function __construct(
        int $status = HttpStatus::BAD_REQUEST,
        string $title = HttpReasonPhrase::BAD_REQUEST,
        iterable $additional = [],
        array $headers = [],
    ) {
        $problem = new ApiProblem($title, 'https://httpstatuses.io/' . $status);
        $problem->setStatus($status);
        foreach ($additional as $key => $value) {
            $problem[$key] = $value;
        }

        parent::__construct($problem->asArray(), $problem->getStatus(), [
            ...$headers,
            HttpHeader::CONTENT_TYPE => ContentType::PROBLEM_DETAILS_JSON,
        ]);
    }
}
