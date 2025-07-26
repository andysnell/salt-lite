<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response\Exceptional\TransformerStrategies;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Response\Exceptional\GenericHttpExceptionResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\HttpExceptionResponseTransformerStrategy;
use PhoneBurner\SaltLite\Http\Response\TextResponse;
use PhoneBurner\SaltLite\Logging\LogTrace;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class TextResponseTransformerStrategy implements HttpExceptionResponseTransformerStrategy
{
    public function transform(
        ResponseInterface $exception,
        ServerRequestInterface $request,
        LogTrace $log_trace,
    ): TextResponse {
        if ($exception instanceof GenericHttpExceptionResponse) {
            $exception = $exception->getWrapped();
        }

        return $exception instanceof TextResponse ? $exception : new TextResponse(
            \sprintf('HTTP %s: %s', $exception->getStatusCode(), $exception->getReasonPhrase()),
            $exception->getStatusCode(),
            [...$exception->getHeaders(), HttpHeader::CONTENT_TYPE => ContentType::TEXT],
        );
    }
}
