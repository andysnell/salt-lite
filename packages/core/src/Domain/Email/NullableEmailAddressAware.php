<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\Email;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface NullableEmailAddressAware
{
    public function getEmailAddress(): EmailAddress|null;
}
