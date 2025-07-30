<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Configuration;

use PhoneBurner\SaltLite\App\Environment;

/**
 * Important: for the sake of serializing the configuration as a PHP array, and
 * leveraging the performance we can get out of opcache keeping that static array
 * in memory, the values of the configuration MUST be limited to scalar types,
 * null, PHP enum cases (since those are just fancy class constants
 *  under the hood), and simple struct-like classes implementing arrays.
 */
interface ConfigurationFactory
{
    public function make(Environment $environment): Configuration;
}
