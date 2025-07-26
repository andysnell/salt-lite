<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Configuration;

use PhoneBurner\SaltLite\App\Environment;
use PhoneBurner\SaltLite\Serialization\VarExport;

use function PhoneBurner\SaltLite\ghost;

/**
 * Important: for the sake of serializing the configuration as a PHP array, and
 * leveraging the performance we can get out of opcache keeping that static array
 * in memory, the values of the configuration MUST be limited to scalar types,
 * null, PHP enum cases (since those are just fancy class constants
 *  under the hood), simple struct-like classes implementing arrays, and .
 */
class ConfigurationFactory
{
    public const string CONFIG_PATH = '/config';
    public const string CACHE_FILE = '/storage/bootstrap/config.cache.php';

    public static function make(Environment $environment): ImmutableConfiguration
    {
        return ghost(static function (ImmutableConfiguration $proxy) use ($environment): void {
            $proxy->__construct(self::compile($environment));
        });
    }

    /**
     * @return array<mixed>
     */
    private static function compile(Environment $environment): array
    {
        $cache_enabled = (bool)$environment->env('SALT_ENABLE_CONFIG_CACHE', true, false);
        $cache_file = $environment->root() . self::CACHE_FILE;

        /**
         * Note: we're intentionally skipping a \file_exists() check here, as we
         * expect opcache to handle this for us, and since the file to usually
         * exist in production, there's a minor performance gain.
         *
         * @phpstan-ignore include.fileNotFound (see https://github.com/phpstan/phpstan/issues/11798)
         */
        $cached_config = @include $cache_file;
        if (\is_array($cached_config)) {
            if ($cache_enabled) {
                return $cached_config;
            }

            // if cache is disabled or the cached file doesn't return a valid PHP
            // array, try to delete the cache file, so that it can be recompiled
            @\unlink($cache_file);
        }

        $config = [];
        foreach (\glob($environment->root() . self::CONFIG_PATH . '/*.php') ?: [] as $file) {
            foreach (include $file ?: [] as $key => $value) {
                $config[$key] = $value;
            }
        }

        VarExport::toFile($cache_file, $config, 'Configuration Cache');

        return $config;
    }
}
