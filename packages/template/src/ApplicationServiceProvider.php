<?php

declare(strict_types=1);

namespace App;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Cache\Lock\LockFactory;
use PhoneBurner\SaltLite\Container\ServiceProvider;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @codeCoverageIgnore
 */
class ApplicationServiceProvider implements ServiceProvider
{
    public static function bind(): array
    {
        return [];
    }

    #[\Override]
    public static function register(App $app): void
    {
        $app->set(
            ApplicationRouteProvider::class,
            static fn(App $app): ApplicationRouteProvider => new ApplicationRouteProvider(),
        );

        $app->set(
            ApplicationScheduleProvider::class,
            static fn(App $app): ApplicationScheduleProvider => new ApplicationScheduleProvider(
                $app->get(CacheItemPoolInterface::class),
                $app->get(LockFactory::class),
                $app->get(EventDispatcher::class),
                $app->get(LoggerInterface::class),
            ),
        );
    }
}
