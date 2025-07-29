<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Framework\App;

use PhoneBurner\SaltLite\App\BuildStage;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\Framework\App\Exception\EnvironmentInitializationFailed;

use const PhoneBurner\SaltLite\Framework\APP_ROOT;
use const PhoneBurner\SaltLite\Framework\CONTEXT;

class EnvironmentLoader
{
    private const array ARGON2_OPTIONS_DEFAULT = [
        'memory_cost' => \PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
        'time_cost' => \PASSWORD_ARGON2_DEFAULT_TIME_COST,
        'thread_cost' => \PASSWORD_ARGON2_DEFAULT_THREADS,
    ];

    private const array ARGON2_OPTIONS_TEST = [
        'memory_cost' => 8,
        'time_cost' => 1,
        'thread_cost' => 1,
    ];

    private const array TEST_ENVIRONMENT_CONSTANTS = [
        'PHPUNIT_COMPOSER_INSTALL',
        'BEHAT_BIN_PATH',
    ];
    private const string BUILD_STAGE_CONSTANT_FQN = 'PhoneBurner\SaltLite\Framework\BUILD_STAGE';
    private const string CONTEXT_CONSTANT_FQN = 'PhoneBurner\SaltLite\Framework\CONTEXT';
    private const string APP_ROOT_CONSTANT_FQN = 'PhoneBurner\SaltLite\Framework\APP_ROOT';
    private const string WEB_ROOT_CONSTANT_FQN = 'PhoneBurner\SaltLite\Framework\WEB_ROOT';
    private const string PASSWORD_ARGON2_OPTIONS_CONSTANT_FQN = 'PhoneBurner\SaltLite\Framework\PASSWORD_ARGON2_OPTIONS';
    private const string UNSERIALIZE_CALLBACK_FQN = '\PhoneBurner\SaltLite\Framework\fail_on_unserialize_undefined_class';

    private static Environment|null $environment = null;

    public static function init(string $app_root = ''): Environment
    {
        if (self::$environment) {
            return self::$environment;
        }

        // Ensure that request timestamp values are set in the $_SERVER superglobal.
        $_SERVER['REQUEST_TIME'] ??= \time();
        $_SERVER['REQUEST_TIME_FLOAT'] ??= \microtime(true);

        $environment = new Environment(
            self::resolveContext(),
            self::resolveBuildStage(),
            self::resolveAppRoot($app_root),
            $_SERVER,
            $_ENV,
        );

        // Override the error reporting settings based on the environment configuration.
        if ($environment->stage !== BuildStage::Production) {
            ErrorReporting::override($_ENV);
        }

        // Define the password hashing options for Argon2 in test environments.
        // These options are less resource-intensive to speed up tests.
        \define(self::PASSWORD_ARGON2_OPTIONS_CONSTANT_FQN, match ($environment->context) {
            Context::Test => self::ARGON2_OPTIONS_TEST,
            default => self::ARGON2_OPTIONS_DEFAULT,
        });

        // Register the application lifecycle teardown method as a shutdown function so
        // that we can ensure that it is called when the script ends, regardless of how
        // it ends, including calls to exit().
        \register_shutdown_function(App::teardown(...));

        // Define a function that will be called when an undefined class is encountered
        // during deserialization, instead of returning a __PHP_Incomplete_Class object.
        // Note that we have to define this function early and cannot define with the
        // other functions in src/functions.php, which are loaded after this file.
        \assert(\function_exists(self::UNSERIALIZE_CALLBACK_FQN));
        \ini_set('unserialize_callback_func', self::UNSERIALIZE_CALLBACK_FQN);

        return self::$environment = $environment;
    }

    private static function resolveAppRoot(string $app_root): string
    {
        if (! \is_dir($app_root)) {
            throw EnvironmentInitializationFailed::withInvalidAppRoot($app_root);
        }

        // Define the application root and web root paths as constants. A lot of
        // the framework code relies on these constants for defining relative and
        // absolute paths, so we need to ensure they are defined early.
        \define('PhoneBurner\SaltLite\Framework\APP_ROOT', $app_root);
        \define(self::WEB_ROOT_CONSTANT_FQN, $app_root . '/public');

        return $app_root;
    }

    private static function resolveContext(): Context
    {
        // Context may already be set if we're running in a test environment
        if (\defined(self::CONTEXT_CONSTANT_FQN)) {
            return \constant(self::CONTEXT_CONSTANT_FQN);
        }

        // Check if we're running in a test environment, which is determined by the presence
        // of certain constants that are typically defined by PHPUnit or Behat.
        // Otherwise, match on the PHP SAPI to determine the context (note that this we use
        // string literals separate from the test constant check here so that PHP optimizes
        // this to a `O(1)` C jump table).
        $context = \array_any(self::TEST_ENVIRONMENT_CONSTANTS, \defined(...)) ? Context::Test : match (\PHP_SAPI) {
            'fpm-fcgi', 'cgi-fcgi', 'cli-server', 'apache2handler', 'apache', => Context::Http,
            'cli', 'phpdbg' => Context::Cli,
            default => throw EnvironmentInitializationFailed::withUnsupportedContext(\PHP_SAPI),
        };

        \define(self::CONTEXT_CONSTANT_FQN, $context);

        return $context;
    }

    private static function resolveBuildStage(): BuildStage
    {
        // Make sure that the build stage is defined and set the same on $_SERVER and $_ENV,
        // If not explicitly set, default to production, but if one is set, it must be a
        // valid build stage.
        $build_stage = BuildStage::parse(
            $_SERVER['SALT_BUILD_STAGE'] ?? $_ENV['SALT_BUILD_STAGE'] ?? BuildStage::Production,
        ) ?? throw EnvironmentInitializationFailed::withUnsupportedBuildStage(
            $_SERVER['SALT_BUILD_STAGE'] ?? $_ENV['SALT_BUILD_STAGE'],
        );

        // Normalize the values in both $_SERVER and $_ENV to the build stage value.
        $_SERVER['SALT_BUILD_STAGE'] = $build_stage->value;
        $_ENV['SALT_BUILD_STAGE'] = $build_stage->value;
        \define(self::BUILD_STAGE_CONSTANT_FQN, $build_stage);

        return $build_stage;
    }
}
