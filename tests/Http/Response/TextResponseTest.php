<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\TextResponse;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TextResponseTest extends TestCase
{
    #[Test]
    public function creates_text_response_with_defaults(): void
    {
        $text = 'Plain text message';
        $response = new TextResponse($text);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame($text, (string)$response->getBody());
        self::assertSame(ContentType::TEXT . '; charset=utf-8', $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }

    #[Test]
    public function creates_text_response_with_custom_status(): void
    {
        $text = 'Resource created successfully';
        $response = new TextResponse($text, HttpStatus::CREATED);

        self::assertSame(HttpStatus::CREATED, $response->getStatusCode());
        self::assertSame($text, (string)$response->getBody());
        self::assertSame(ContentType::TEXT . '; charset=utf-8', $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }

    #[Test]
    public function creates_text_response_with_custom_headers(): void
    {
        $text = 'Plain text with custom headers';
        $headers = [
            'X-Custom-Header' => ['custom-value'],
            HttpHeader::CACHE_CONTROL => ['no-cache'],
        ];

        $response = new TextResponse($text, HttpStatus::OK, $headers);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame($text, (string)$response->getBody());
        self::assertSame(ContentType::TEXT . '; charset=utf-8', $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame(['custom-value'], $response->getHeader('X-Custom-Header'));
        self::assertSame(['no-cache'], $response->getHeader(HttpHeader::CACHE_CONTROL));
    }

    #[Test]
    public function content_type_header_can_be_overridden(): void
    {
        $text = 'Plain text with custom content type';
        $customContentType = 'text/csv';
        $headers = [
            HttpHeader::CONTENT_TYPE => $customContentType,
        ];

        $response = new TextResponse($text, HttpStatus::OK, $headers);

        self::assertSame(HttpStatus::OK, $response->getStatusCode());
        self::assertSame($text, (string)$response->getBody());
        self::assertSame($customContentType, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }
}
