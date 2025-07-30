<?php

declare(strict_types=1);

use Symplify\MonorepoBuilder\Config\MBConfig;
use Symplify\MonorepoBuilder\Merge\JsonSchema;

return static function (MBConfig $config): void {
    $config->packageDirectories([__DIR__ . '/packages']);
    $config->defaultBranch('main');
    $config->composerSectionOrder(JsonSchema::getProperties());
};
