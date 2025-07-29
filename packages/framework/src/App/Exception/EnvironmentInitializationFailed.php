<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Framework\App\Exception;

final class EnvironmentInitializationFailed extends \LogicException
{
    public static function withInvalidAppRoot(string $app_root): self
    {
        return new self($app_root . ' is not a valid application root directory.');
    }

    public static function withUnsupportedContext(string $php_sapi): self
    {
        return new self(\sprintf('Unsupported PHP SAPI "%s".', $php_sapi));
    }

    public static function withUnsupportedBuildStage(string $stage): self
    {
        return new self(\sprintf('Unsupported Build Stage "%s".', $stage));
    }
}
