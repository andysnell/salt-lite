<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App;

use PhoneBurner\SaltLite\App\Environment;
use PhoneBurner\SaltLite\Configuration\Configuration;
use PhoneBurner\SaltLite\Container\InvokingContainer;
use PhoneBurner\SaltLite\Container\MutableContainer;
use PhoneBurner\SaltLite\Container\ServiceContainer;

/**
 * This is the main application class. It is a container that holds context,
 * environment state, configuration, and services. It should be the only singleton
 * service in the application, so that tearing it can result in complete garbage
 * collection and reduce the possibility of memory leaks or stale/shared state.
 *
 * While the class is a container, it is not intended to be used as a general-purpose
 * service container itself. The implemented container methods are really shortcuts to
 * the underlying service container.
 *
 * @extends MutableContainer<mixed>
 */
interface App extends MutableContainer, InvokingContainer
{
    // phpcs:ignore
    public Context $context { get; }

    // phpcs:ignore
    public Environment $environment { get; }

    // phpcs:ignore
    public ServiceContainer $services { get; }

    // phpcs:ignore
    public Configuration $config { get; }

    public static function booted(): bool;

    public static function instance(): self;

    public static function bootstrap(Context $context): self;

    public static function teardown(): null;

/**
     * Wrap a callback in the context of an application lifecycle instance. Note
     * that if exit() is called within the callback, the application will still be
     * torn down properly, because App::teardown(...) is registered as a shutdown
     * function.
     *
     * @template T
     * @param callable(self): T $callback
     * @return T
     */
    public static function exec(Context $context, callable $callback): mixed;
}
