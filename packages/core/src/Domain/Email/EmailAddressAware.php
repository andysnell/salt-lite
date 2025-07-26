<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\Email;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
interface EmailAddressAware extends NullableEmailAddressAware
{
    public function getEmailAddress(): EmailAddress;
}
