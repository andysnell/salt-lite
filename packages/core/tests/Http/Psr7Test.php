<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http;

use PhoneBurner\SaltLite\Http\Domain\HttpMethod;
use PhoneBurner\SaltLite\Http\Psr7;
use PhoneBurner\SaltLite\Http\RequestFactory;
use PhoneBurner\SaltLite\String\Str;
use PhoneBurner\SaltLite\Tests\Fixtures\MockRequestHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Psr7Test extends TestCase
{
    #[Test]
    public function attributeReturnsNullWhenAttributeNotFound(): void
    {
        $request = new RequestFactory()->server(HttpMethod::Get, 'http://example.com');

        self::assertNull(Psr7::attribute(RequestHandlerInterface::class, $request));
    }

    #[Test]
    public function attributeReturnsNullWhenAttributeNotInstance(): void
    {
        $request = new RequestFactory()->server(HttpMethod::Get, 'http://example.com', attributes: [
            RequestHandlerInterface::class => new \stdClass(),
        ]);

        self::assertNull(Psr7::attribute(RequestHandlerInterface::class, $request));
    }

    #[Test]
    public function attributeReturnsInstanceOnHappyPath(): void
    {
        $handler = new MockRequestHandler();
        $request = new RequestFactory()->server(HttpMethod::Get, 'http://example.com', attributes: [
            RequestHandlerInterface::class => $handler,
        ]);

        self::assertSame($handler, Psr7::attribute(RequestHandlerInterface::class, $request));
    }

    #[Test]
    public function jsonBodyToArrayHappyPathWithMessage(): void
    {
        $array = [
            'foo' => 'bar',
            'baz' => 42,
        ];

        $message = $this->createMock(MessageInterface::class);
        $message->method('getBody')->willReturn(Str::stream(\json_encode($array, \JSON_THROW_ON_ERROR)));

        self::assertSame($array, Psr7::jsonBodyToArray($message));
    }

    #[Test]
    public function jsonBodyToArrayHappyPathWithStream(): void
    {
        $array = [
            'foo' => 'bar',
            'baz' => 42,
        ];

        $stream = Str::stream(\json_encode($array, \JSON_THROW_ON_ERROR));

        self::assertSame($array, Psr7::jsonBodyToArray($stream));
    }

    #[Test]
    public function jsonBodyToArrayHappySadPathInvalid(): void
    {
        $array = [
            'foo' => 'bar',
            'baz' => 42,
        ];

        $stream = Str::stream(\substr(\json_encode($array, \JSON_THROW_ON_ERROR), 0, -1));

        self::assertNull(Psr7::jsonBodyToArray($stream));
    }

    #[Test]
    public function jsonBodyToArrayHappySadPathNotArray(): void
    {
        $stream = Str::stream(\substr(\json_encode('false', \JSON_THROW_ON_ERROR), 0, -1));

        self::assertNull(Psr7::jsonBodyToArray($stream));
    }

    #[Test]
    public function expectsReturnsTrueWhenContentTypeMatchesAcceptHeader(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeaderLine')
            ->willReturnMap([
                ['Accept', 'application/json'],
                ['Content-Type', ''],
            ]);

        self::assertTrue(Psr7::expects($message, 'application/json'));
    }

    #[Test]
    public function expectsReturnsTrueWhenContentTypeMatchesContentTypeHeader(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeaderLine')
            ->willReturnMap([
                ['Accept', ''],
                ['Content-Type', 'application/json'],
            ]);

        self::assertTrue(Psr7::expects($message, 'application/json'));
    }

    #[Test]
    public function expectsReturnsTrueWhenContentTypeMatchesWithStructuredSyntaxSuffix(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeaderLine')
            ->willReturnMap([
                ['Accept', 'application/vnd.api+json'],
                ['Content-Type', ''],
            ]);

        self::assertTrue(Psr7::expects($message, 'application/json'));
    }

    #[Test]
    public function expectsReturnsFalseWhenNoMatchingContentType(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeaderLine')
            ->willReturnMap([
                ['Accept', 'text/html'],
                ['Content-Type', 'text/plain'],
            ]);

        self::assertFalse(Psr7::expects($message, 'application/json'));
    }

    #[Test]
    public function expectsReturnsFalseWhenHeadersAreEmpty(): void
    {
        $message = $this->createMock(MessageInterface::class);
        $message->method('getHeaderLine')
            ->willReturnMap([
                ['Accept', ''],
                ['Content-Type', ''],
            ]);

        self::assertFalse(Psr7::expects($message, 'application/json'));
    }
}
