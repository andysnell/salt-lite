<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Response;

use Laminas\Diactoros\Response;
use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Stream\IteratorStream;
use PhoneBurner\SaltLite\Time\TimeInterval\TimeInterval;

class ServerSentEventsResponse extends Response
{
    public const array DEFAULT_HEADERS = [
        HttpHeader::X_ACCEL_BUFFERING => 'no',
        HttpHeader::CONTENT_TYPE => ContentType::EVENT_STREAM,
        HttpHeader::CACHE_CONTROL => 'no-cache',
        HttpHeader::CONNECTION => 'keep-alive',
    ];

    public function __construct(
        iterable $iterator,
        public TimeInterval $ttl = new TimeInterval(minutes: 10),
        array $headers = self::DEFAULT_HEADERS,
    ) {
        parent::__construct(new IteratorStream($iterator), HttpStatus::OK, $headers);
    }
}
