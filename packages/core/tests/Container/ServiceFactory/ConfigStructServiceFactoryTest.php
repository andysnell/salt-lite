<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\Configuration\ImmutableConfiguration;
use PhoneBurner\SaltLite\Container\ServiceFactory\ConfigStructServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\MockApp;
use PhoneBurner\SaltLite\Tests\Fixtures\TestApiKeyConfigStruct;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigStructServiceFactoryTest extends TestCase
{
    #[Test]
    public function itResolvesConfigStructFromApp(): void
    {
        $config_struct = new TestApiKeyConfigStruct('foo');
        $app = new MockApp(config: new ImmutableConfiguration([
            'test' => $config_struct,
        ]));

        $factory = new ConfigStructServiceFactory('test');

        self::assertSame($config_struct, $factory($app, TestApiKeyConfigStruct::class));
    }
}
