<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http;

use Laminas\Diactoros\Stream;
use Laminas\Diactoros\Uri;
use PhoneBurner\SaltLite\Domain\IpAddress\IpAddress;
use PhoneBurner\SaltLite\Http\Domain\HttpMethod;
use PhoneBurner\SaltLite\Http\RequestFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;

final class RequestFactoryTest extends TestCase
{
    private RequestFactory $factory;

    protected function setUp(): void
    {
        $this->factory = new RequestFactory();
    }

    #[Test]
    public function createRequestWithStringUri(): void
    {
        $uri = 'https://example.com/test';
        $request = $this->factory->createRequest(HttpMethod::Get, $uri);

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('GET', $request->getMethod());
        self::assertSame($uri, (string)$request->getUri());
    }

    #[Test]
    public function createRequestWithUriObject(): void
    {
        $uri = new Uri('https://example.com/test');
        $request = $this->factory->createRequest(HttpMethod::Post, $uri);

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame($uri, $request->getUri());
    }

    #[Test]
    public function createRequestWithStringMethod(): void
    {
        $request = $this->factory->createRequest('PUT', 'https://example.com/test');

        self::assertInstanceOf(RequestInterface::class, $request);
        self::assertSame('PUT', $request->getMethod());
    }

    #[Test]
    public function createRequestWithCustomBodyAndHeaders(): void
    {
        $body = new Stream('php://temp', 'w+b');
        $body->write('test body');

        $headers = ['Content-Type' => 'application/json', 'X-Test' => ['value1', 'value2']];

        $request = $this->factory->createRequest(
            HttpMethod::Post,
            'https://example.com/test',
            $headers,
            $body,
        );

        self::assertSame('test body', (string)$request->getBody());
        self::assertSame(['application/json'], $request->getHeader('Content-Type'));
        self::assertSame(['value1', 'value2'], $request->getHeader('X-Test'));
    }

    #[Test]
    public function createServerRequestWithStringUri(): void
    {
        $uri = 'https://example.com/test';
        $request = $this->factory->createServerRequest(HttpMethod::Get, $uri);

        self::assertInstanceOf(ServerRequestInterface::class, $request);
        self::assertSame('GET', $request->getMethod());
        self::assertSame($uri, (string)$request->getUri());
    }

    #[Test]
    public function createServerRequestWithUriObject(): void
    {
        $uri = new Uri('https://example.com/test');
        $request = $this->factory->createServerRequest(HttpMethod::Post, $uri);

        self::assertInstanceOf(ServerRequestInterface::class, $request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame($uri, $request->getUri());
    }

    #[Test]
    public function createServerRequestWithServerParams(): void
    {
        $serverParams = ['SERVER_NAME' => 'example.com', 'REMOTE_ADDR' => '127.0.0.1'];
        $request = $this->factory->createServerRequest(
            HttpMethod::Get,
            'https://example.com/test',
            $serverParams,
        );

        self::assertSame($serverParams, $request->getServerParams());
    }

    #[Test]
    public function serverCreatesServerRequestWithAllOptions(): void
    {
        $uri = new Uri('https://example.com/test');
        $body = new Stream('php://temp', 'w+b');
        $body->write('test body');

        $headers = ['Content-Type' => 'application/json'];
        $server = ['SERVER_NAME' => 'example.com'];
        $query = ['param1' => 'value1'];
        $cookies = ['cookie1' => 'value1'];
        $files = [];
        $parsed = ['parsed_body' => 'value'];
        $protocol = '2.0';
        $attributes = ['attr1' => 'value1'];

        $request = $this->factory->server(
            HttpMethod::Post,
            $uri,
            $body,
            $headers,
            $server,
            $query,
            $cookies,
            $files,
            $parsed,
            $protocol,
            $attributes,
        );

        self::assertInstanceOf(ServerRequestInterface::class, $request);
        self::assertSame('POST', $request->getMethod());
        self::assertSame($uri, $request->getUri());
        self::assertSame('test body', (string)$request->getBody());
        self::assertSame(['application/json'], $request->getHeader('Content-Type'));
        self::assertSame($server, $request->getServerParams());
        self::assertSame($query, $request->getQueryParams());
        self::assertSame($cookies, $request->getCookieParams());
        self::assertSame($files, $request->getUploadedFiles());
        self::assertSame($parsed, $request->getParsedBody());
        self::assertSame($protocol, $request->getProtocolVersion());
        self::assertSame('value1', $request->getAttribute('attr1'));
    }

    #[Test]
    public function fromGlobalsAddsIpAddressAttribute(): void
    {
        // Save original server vars
        $original_server = $_SERVER;

        try {
            $_SERVER['REMOTE_ADDR'] = '192.168.1.1';

            $request = $this->factory->fromGlobals();

            self::assertInstanceOf(ServerRequestInterface::class, $request);
            $ip = $request->getAttribute(IpAddress::class);

            self::assertInstanceOf(IpAddress::class, $ip);
            self::assertSame('192.168.1.1', (string)$ip);
        } finally {
            // Restore original server vars
            $_SERVER = $original_server;
        }
    }
}
