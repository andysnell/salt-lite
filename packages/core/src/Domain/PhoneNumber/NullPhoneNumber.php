<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

/**
 * Value object representing an empty phone number.
 */
#[Contract]
final readonly class NullPhoneNumber implements NullablePhoneNumber, NullablePhoneNumberAware
{
    public static function make(): self
    {
        static $cache = new self();
        return $cache;
    }

    #[\Override]
    public function getPhoneNumber(): self
    {
        return $this;
    }

    #[\Override]
    public function toE164(): E164|null
    {
        return null;
    }
}
