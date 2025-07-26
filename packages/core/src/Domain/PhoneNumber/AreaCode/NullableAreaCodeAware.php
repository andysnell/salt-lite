<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface NullableAreaCodeAware
{
    public function getAreaCode(): AreaCode|null;
}
