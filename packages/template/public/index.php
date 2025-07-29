<?php

declare(strict_types=1);

use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\App\Kernel;
use PhoneBurner\SaltLite\Framework\App\App;

require_once match (true) {
    \file_exists(__DIR__ . '/../vendor/autoload.php') => __DIR__ . '/../vendor/autoload.php',
    \file_exists(__DIR__ . '/../../../vendor/autoload.php') => __DIR__ . '/../../../vendor/autoload.php',
    default => throw new \LogicException('Unable to find the vendor autoload file.'),
};

require_once __DIR__ . '/../src/bootstrap.php';

App::exec(Context::Http, static function (App $app): void {
    $app->get(Kernel::class)->run();
});
