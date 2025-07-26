<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Domain\IpAddress\IpAddressType;

final readonly class IpAddressTestStruct
{
    public function __construct(
        public string $value,
        public IpAddressType $type,
    ) {
    }
}
