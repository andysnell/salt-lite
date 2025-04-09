<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Event;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use PhoneBurner\SaltLite\Http\Event\HandlingHttpRequestComplete;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HandlingHttpRequestCompleteTest extends TestCase
{
    #[Test]
    public function constructor_sets_public_properties(): void
    {
        $request = new ServerRequest();
        $response = new Response();
        $event = new HandlingHttpRequestComplete($request, $response);

        self::assertSame($request, $event->request);
        self::assertSame($response, $event->response);
    }
}
