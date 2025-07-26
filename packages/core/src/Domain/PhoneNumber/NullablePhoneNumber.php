<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface NullablePhoneNumber
{
    public function toE164(): E164|null;
}
