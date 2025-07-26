<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\Exceptional\TooManyRequestsResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TooManyRequestsResponseTest extends TestCase
{
    #[Test]
    public function responseHasExpectedDefaults(): void
    {
        $sut = new TooManyRequestsResponse();

        self::assertSame(HttpStatus::TOO_MANY_REQUESTS, $sut->getStatusCode());
        self::assertSame(HttpReasonPhrase::TOO_MANY_REQUESTS, $sut->getStatusTitle());
        self::assertSame('', $sut->getStatusDetail());
        self::assertSame([], $sut->getAdditional());

        self::assertSame(HttpStatus::TOO_MANY_REQUESTS, $sut->getCode());
        self::assertSame('HTTP 429: Too Many Requests', $sut->getMessage());

        self::assertSame('text/plain; charset=utf-8', $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame('HTTP 429: Too Many Requests', $sut->getBody()->getContents());
    }
}
