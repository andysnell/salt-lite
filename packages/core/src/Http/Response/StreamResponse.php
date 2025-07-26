<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response;

use Laminas\Diactoros\Response;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\String\Str;
use Psr\Http\Message\StreamInterface;

class StreamResponse extends Response
{
    /**
     * Creates a stream response from a string body or stream, in contrast to the
     * constructor which accepts a stream, stream identifier string, or resource.
     *
     * @param array<string, string|array<string>> $headers
     */
    public static function make(string|StreamInterface $stream, int $status = HttpStatus::OK, array $headers = []): self
    {
        return new self(Str::stream($stream), $status, $headers);
    }
}
