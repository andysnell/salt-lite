<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\Exceptional\MethodNotAllowedResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MethodNotAllowedResponseTest extends TestCase
{
    #[Test]
    public function responseHasExpectedDefaults(): void
    {
        $sut = new MethodNotAllowedResponse();

        self::assertSame(HttpStatus::METHOD_NOT_ALLOWED, $sut->getStatusCode());
        self::assertSame(HttpReasonPhrase::METHOD_NOT_ALLOWED, $sut->getStatusTitle());
        self::assertSame('', $sut->getStatusDetail());
        self::assertSame([], $sut->getAdditional());

        self::assertSame(HttpStatus::METHOD_NOT_ALLOWED, $sut->getCode());
        self::assertSame('HTTP 405: Method Not Allowed', $sut->getMessage());

        self::assertSame('text/plain; charset=utf-8', $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame('HTTP 405: Method Not Allowed', $sut->getBody()->getContents());
    }
}
