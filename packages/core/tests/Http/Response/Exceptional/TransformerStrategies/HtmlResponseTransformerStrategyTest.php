<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response\Exceptional\TransformerStrategies;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\Exceptional\HttpExceptionResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\TransformerStrategies\HtmlResponseTransformerStrategy;
use PhoneBurner\SaltLite\Http\Response\HtmlResponse;
use PhoneBurner\SaltLite\Logging\LogTrace;
use PhoneBurner\SaltLite\Uuid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class HtmlResponseTransformerStrategyTest extends TestCase
{
    private HtmlResponseTransformerStrategy $strategy;
    private LogTrace $log_trace;

    protected function setUp(): void
    {
        $this->strategy = new HtmlResponseTransformerStrategy();
        $this->log_trace = new LogTrace(Uuid::instance('d1dd4364-d933-4cb3-b158-6340ccd35d47'));
    }

    #[Test]
    public function transformCreatesHtmlResponse(): void
    {
        $exception = $this->createMock(HttpExceptionResponse::class);
        $exception->method('getStatusCode')->willReturn(HttpStatus::NOT_FOUND);
        $exception->method('getStatusTitle')->willReturn('Not Found');
        $exception->method('getStatusDetail')->willReturn('The requested resource was not found');
        $exception->method('getHeaders')->willReturn(['X-Test' => ['test-value']]);
        $exception->method('getAdditional')->willReturn([]);

        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->strategy->transform($exception, $request, $this->log_trace);

        self::assertInstanceOf(HtmlResponse::class, $response);
        self::assertSame(HttpStatus::NOT_FOUND, $response->getStatusCode());
        self::assertSame(ContentType::HTML, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame(['test-value'], $response->getHeader('X-Test'));

        $body = (string)$response->getBody();
        self::assertStringContainsString('<!doctype html>', $body);
        self::assertStringContainsString('<title>Not Found</title>', $body);
        self::assertStringContainsString('<h1 class="mb-3">Not Found</h1>', $body);
        self::assertStringContainsString('<p class="mb-4">The requested resource was not found</p>', $body);
        self::assertStringContainsString('d1dd4364-d933-4cb3-b158-6340ccd35d47', $body);
        self::assertStringContainsString('404', $body);
    }
}
