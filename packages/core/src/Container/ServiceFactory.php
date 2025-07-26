<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface ServiceFactory
{
    /**
     * @param class-string $id
     */
    public function __invoke(App $app, string $id): object;
}
