<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response\Exceptional;

use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\Exceptional\AuthorizationRequiredResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AuthorizationRequiredResponseTest extends TestCase
{
    #[Test]
    public function responseHasExpectedDefaults(): void
    {
        $sut = new AuthorizationRequiredResponse();

        self::assertSame(HttpStatus::FORBIDDEN, $sut->getStatusCode());
        self::assertSame('Authorization Required', $sut->getStatusTitle());
        self::assertSame('You do not have permission to access the requested resource.', $sut->getStatusDetail());
        self::assertSame([], $sut->getAdditional());

        self::assertSame(HttpStatus::FORBIDDEN, $sut->getCode());
        self::assertSame('HTTP 403: Forbidden', $sut->getMessage());

        self::assertSame('text/plain; charset=utf-8', $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame('HTTP 403: Forbidden', $sut->getBody()->getContents());
    }
}
