<?php

declare(strict_types=1);

use PhoneBurner\SaltLite\App\App as AppContract;
use PhoneBurner\SaltLite\App\Kernel;
use PhoneBurner\SaltLite\Framework\App\App;
use PhoneBurner\SaltLite\Framework\App\EnvironmentLoader;

require_once match (true) {
    \file_exists(__DIR__ . '/../vendor/autoload.php') => __DIR__ . '/../vendor/autoload.php',
    \file_exists(__DIR__ . '/../../../vendor/autoload.php') => __DIR__ . '/../../../vendor/autoload.php',
    default => throw new \LogicException('Unable to find the vendor autoload file.'),
};

App::exec(EnvironmentLoader::instance(), static function (AppContract $app): void {
    $app->get(Kernel::class)->run();
});
