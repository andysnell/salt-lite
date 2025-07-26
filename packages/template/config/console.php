<?php

declare(strict_types=1);

use App\Example\Command\ExampleCommand;
use PhoneBurner\SaltLite\Framework\Console\Config\ConsoleConfigStruct;
use PhoneBurner\SaltLite\Framework\Console\Config\ShellConfigStruct;

return [
    'console' => new ConsoleConfigStruct(
        commands: [
            ExampleCommand::class,
        ],
        shell: new ShellConfigStruct(),
    ),
];
