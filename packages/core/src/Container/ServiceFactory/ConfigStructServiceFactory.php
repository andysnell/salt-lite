<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Configuration\ConfigStruct;
use PhoneBurner\SaltLite\Container\ServiceFactory;
use PhoneBurner\SaltLite\Type\Type;

final readonly class ConfigStructServiceFactory implements ServiceFactory
{
    public function __construct(private string $name)
    {
    }

    public function __invoke(App $app, string $id): ConfigStruct
    {
        return Type::of(ConfigStruct::class, $app->config->get($this->name));
    }
}
