<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

final readonly class DotAccessTestStruct
{
    public function __construct(
        public string $needle,
        public bool $exists,
        public mixed $expected,
        public mixed $default = null,
    ) {
    }
}
