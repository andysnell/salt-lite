<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional\TransformerStrategies;

use PhoneBurner\SaltLite\Http\Response\ApiProblemResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\HttpExceptionResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\HttpExceptionResponseTransformerStrategy;
use PhoneBurner\SaltLite\Logging\LogTrace;
use Psr\Http\Message\ServerRequestInterface;

final class JsonResponseTransformerStrategy implements HttpExceptionResponseTransformerStrategy
{
    public function transform(
        HttpExceptionResponse $exception,
        ServerRequestInterface $request,
        LogTrace $log_trace,
    ): ApiProblemResponse {
        return new ApiProblemResponse($exception->getStatusCode(), $exception->getStatusTitle(), [
            'log_trace' => $log_trace->toString(),
            'detail' => $exception->getStatusDetail() ?: null,
            ...$exception->getAdditional(),
        ], $exception->getHeaders());
    }
}
