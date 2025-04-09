<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Event;

use Laminas\Diactoros\Response;
use PhoneBurner\SaltLite\Http\Event\EmittingHttpResponseComplete;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmittingHttpResponseCompleteTest extends TestCase
{
    #[Test]
    public function constructor_sets_public_properties(): void
    {
        $response = new Response();
        $event = new EmittingHttpResponseComplete($response);

        self::assertSame($response, $event->request);
    }
}
