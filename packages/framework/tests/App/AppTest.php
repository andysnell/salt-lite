<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Framework\Tests\App;

use App\Tests\Unit\TestSupport\MockEventDispatcher;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\Framework\App\App;
use PhoneBurner\SaltLite\Http\Response\Exceptional\TransformerStrategies\JsonResponseTransformerStrategy;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\EventDispatcher\EventDispatcherInterface;

final class AppTest extends TestCase
{
    #[Test]
    public function applicationLifecycleHappyPath(): void
    {
        $this->markTestSkipped('Figure this out later, it is not working with the monorepo and config loading.');
        $app = App::bootstrap(Context::Test);
        $app->services->set(EventDispatcherInterface::class, new class implements EventDispatcherInterface
        {
            public function __construct(public array $dispatched = [])
            {
            }

            #[\Override]
            public function dispatch(object $event): object
            {
                return $this->dispatched[] = $event;
            }
        });
        self::assertTrue(App::booted());
        self::assertSame($app, App::instance());

        self::assertSame('SaltLite Framework', $app->config->get('app.name'));
        self::assertSame(JsonResponseTransformerStrategy::class, $app->config->get('http.exceptional_response_default_transformer'));

        self::assertTrue($app->has(JsonResponseTransformerStrategy::class));
        self::assertInstanceOf(JsonResponseTransformerStrategy::class, $app->get(JsonResponseTransformerStrategy::class));

        self::assertSame(42, $app->call(static fn (): int => 42));

        self::assertSame($app, $app->call(static function (App $arg): App {
            self::assertSame(App::instance(), $arg);
            return $arg;
        }));

        $invokable = new class {
            public function __invoke(JsonResponseTransformerStrategy $strategy): int
            {
                TestCase::assertInstanceOf(JsonResponseTransformerStrategy::class, $strategy);
                return 42;
            }

            public function foo(): string
            {
                return "Hello, World!";
            }
        };

        self::assertSame(42, $app->call($invokable));
        self::assertSame('Hello, World!', $app->call($invokable, 'foo'));

        App::teardown();
        self::assertFalse(App::booted());
    }
}
