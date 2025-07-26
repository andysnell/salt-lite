<?php

declare(strict_types=1);

namespace App\Tests\Unit\TestSupport;

use PhoneBurner\SaltLite\Http\Domain\ContentType;
use PhoneBurner\SaltLite\Http\Domain\HttpHeader;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;

class HalApiValidator
{
    public static function assert(int $code, string $file, ResponseInterface|null $response): void
    {
        TestCase::assertInstanceOf(ResponseInterface::class, $response);
        TestCase::assertJsonStringEqualsJsonFile($file, (string)$response->getBody());
        TestCase::assertSame($code, $response->getStatusCode());
        TestCase::assertSame(match (true) {
            $code >= 200 && $code < 300 => ContentType::HAL_JSON,
            $code >= 400 => ContentType::PROBLEM_DETAILS_JSON,
            default => '',
        }, $response->getHeaderLine(HttpHeader::CONTENT_TYPE));
    }
}
