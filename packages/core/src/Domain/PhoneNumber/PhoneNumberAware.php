<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface PhoneNumberAware extends NullablePhoneNumberAware
{
    public function getPhoneNumber(): PhoneNumber;
}
