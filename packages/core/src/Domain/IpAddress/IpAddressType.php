<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\IpAddress;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
enum IpAddressType
{
    case IPv4;
    case IPv6;
}
