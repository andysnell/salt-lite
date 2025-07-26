<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\Exception;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

#[Contract]
class InvalidPhoneNumber extends \RuntimeException
{
}
