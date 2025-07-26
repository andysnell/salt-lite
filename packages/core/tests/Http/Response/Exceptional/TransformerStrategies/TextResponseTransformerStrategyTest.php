<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Response\Exceptional\TransformerStrategies;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PhoneBurner\SaltLite\Http\Domain\HttpStatus;
use PhoneBurner\SaltLite\Http\Response\Exceptional\NotFoundResponse;
use PhoneBurner\SaltLite\Http\Response\Exceptional\TransformerStrategies\TextResponseTransformerStrategy;
use PhoneBurner\SaltLite\Http\Response\TextResponse;
use PhoneBurner\SaltLite\Logging\LogTrace;
use PhoneBurner\SaltLite\Uuid\Uuid;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

final class TextResponseTransformerStrategyTest extends TestCase
{
    private TextResponseTransformerStrategy $strategy;

    private LogTrace $log_trace;

    protected function setUp(): void
    {
        $this->strategy = new TextResponseTransformerStrategy();
        $this->log_trace = new LogTrace(Uuid::instance('d1dd4364-d933-4cb3-b158-6340ccd35d47'));
    }

    #[Test]
    public function transformCreatesApiProblemResponse(): void
    {
        $exception = new NotFoundResponse();
        $request = $this->createMock(ServerRequestInterface::class);

        $response = $this->strategy->transform($exception, $request, $this->log_trace);

        self::assertInstanceOf(TextResponse::class, $response);
        self::assertSame(HttpStatus::NOT_FOUND, $response->getStatusCode());
        self::assertSame(ContentType::TEXT . '; charset=utf-8', $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
        self::assertSame('HTTP 404: Not Found', (string)$response->getBody());
    }
}
