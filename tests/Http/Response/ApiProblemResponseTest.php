<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpReasonPhrase;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\ApiProblemResponse;
use PhoneBurner\SaltLite\Serialization\Json;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApiProblemResponseTest extends TestCase
{
    #[Test]
    public function response_has_expected_defaults(): void
    {
        $sut = new ApiProblemResponse();

        self::assertSame(400, $sut->getStatusCode());
        self::assertSame(ContentType::PROBLEM_DETAILS_JSON, $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));

        $body = Json::decode((string)$sut->getBody());
        self::assertSame(HttpStatus::BAD_REQUEST, $body['status']);
        self::assertSame(HttpReasonPhrase::BAD_REQUEST, $body['title']);
        self::assertSame('https://httpstatuses.io/400', $body['type']);
    }

    #[Test]
    public function response_can_include_additional_parameters(): void
    {
        $additional = [
            'detail' => 'Invalid input provided for authentication',
            'instance' => '/api/users/123',
        ];

        $sut = new ApiProblemResponse(HttpStatus::FORBIDDEN, HttpReasonPhrase::FORBIDDEN, $additional);

        self::assertSame(HttpStatus::FORBIDDEN, $sut->getStatusCode());
        self::assertSame(ContentType::PROBLEM_DETAILS_JSON, $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));

        $body = Json::decode((string)$sut->getBody());
        self::assertSame(HttpStatus::FORBIDDEN, $body['status']);
        self::assertSame(HttpReasonPhrase::FORBIDDEN, $body['title']);
        self::assertSame('Invalid input provided for authentication', $body['detail']);
        self::assertSame('/api/users/123', $body['instance']);
        self::assertSame('https://httpstatuses.io/403', $body['type']);
    }

    #[Test]
    public function can_apply_custom_headers(): void
    {
        $headers = [
            'X-Custom-Header' => 'custom-value',
        ];

        $sut = new ApiProblemResponse(400, 'Bad Request', [], $headers);

        self::assertSame(400, $sut->getStatusCode());
        self::assertSame(ContentType::PROBLEM_DETAILS_JSON, $sut->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame('custom-value', $sut->getHeaderLine('X-Custom-Header'));

        $body = Json::decode((string)$sut->getBody());
        self::assertSame(HttpStatus::BAD_REQUEST, $body['status']);
        self::assertSame(HttpReasonPhrase::BAD_REQUEST, $body['title']);
        self::assertSame('https://httpstatuses.io/400', $body['type']);
    }
}
