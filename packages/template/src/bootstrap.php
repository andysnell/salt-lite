<?php

declare(strict_types=1);

namespace App;

use PhoneBurner\SaltLite\App\BuildStage;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\Framework\App\App;
use PhoneBurner\SaltLite\Framework\App\ErrorReporting;

use const PhoneBurner\SaltLite\Framework\APP_ROOT;
use const PhoneBurner\SaltLite\Framework\CONTEXT;

(static function (): void {
    // Define some critical constants that are used throughout the application. The
    // APP_ROOT constant is defined based on the location of this file, which allows
    // this to work regardless of whether the application is installed as a dependency
    // or as a standalone project (e.g. for framework development).
    \define('PhoneBurner\SaltLite\Framework\START_MICROTIME', \microtime(true));
    \define('PhoneBurner\SaltLite\Framework\APP_ROOT', \dirname(__DIR__, \str_contains(__DIR__, 'vendor') ? 4 : 1));
    \define('PhoneBurner\SaltLite\Framework\WEB_ROOT', APP_ROOT . '/public');

    // Make sure that the build stage is defined and set the same on $_SERVER and $_ENV,
    // If not explicitly set, default to production.
    $build_stage = BuildStage::instance($_SERVER['SALT_BUILD_STAGE']
        ?? $_ENV['SALT_BUILD_STAGE']
        ?? BuildStage::Production);

    $_SERVER['SALT_BUILD_STAGE'] = $build_stage->value;
    $_ENV['SALT_BUILD_STAGE'] = $build_stage->value;

    // Handle build-stage specific overrides for error reporting and other settings,
    // including reducing the overhead of password hashing in test environments.
    if ($build_stage !== BuildStage::Production) {
        // Override the error reporting settings based on the environment configuration.
        ErrorReporting::override($_ENV);

        $is_test_environment = \defined('PHPUNIT_COMPOSER_INSTALL')
            || \defined('BEHAT_BIN_PATH')
            || (\defined('PhoneBurner\SaltLite\Framework\CONTEXT') && CONTEXT === Context::Test);

        // Check if we're running in a PHPUnit context.
        if ($is_test_environment) {
            // Define the context as Test if not already defined.
            if (! \defined('PhoneBurner\SaltLite\Framework\CONTEXT')) {
                \define('PhoneBurner\SaltLite\Framework\CONTEXT', Context::Test);
            }

            // Define the password hashing options for Argon2 in test environments.
            // These options are less resource-intensive to speed up tests.
            if (! \defined('PhoneBurner\SaltLite\Framework\PASSWORD_ARGON2_OPTIONS')) {
                \define('PhoneBurner\SaltLite\Framework\PASSWORD_ARGON2_OPTIONS', [
                    'memory_cost' => 8,
                    'time_cost' => 1,
                    'thread_cost' => 1,
                ]);
            }
        }
    } elseif (! \defined('PhoneBurner\SaltLite\Framework\PASSWORD_ARGON2_OPTIONS')) {
        \define('PhoneBurner\SaltLite\Framework\PASSWORD_ARGON2_OPTIONS', [
            'memory_cost' => \PASSWORD_ARGON2_DEFAULT_MEMORY_COST,
            'time_cost' => \PASSWORD_ARGON2_DEFAULT_TIME_COST,
            'thread_cost' => \PASSWORD_ARGON2_DEFAULT_THREADS,
        ]);
    }

    // Register the application lifecycle teardown method as a shutdown function so
    // that we can ensure that it is called when the script ends, regardless of how
    // it ends, including calls to exit().
    \register_shutdown_function(App::teardown(...));
})();

// Define a function that will be called when an undefined class is encountered
// during deserialization, instead of returning a __PHP_Incomplete_Class object.
// Note that we have to define this function early and cannot define with the
// other functions in src/functions.php, which are loaded after this file.
function fail_on_unserialize_undefined_class(string $class): never
{
    throw new \DomainException('Class not found: ' . $class);
}

\ini_set('unserialize_callback_func', 'App\fail_on_unserialize_undefined_class');

