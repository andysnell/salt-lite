<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\KeyManagement\Key;

interface SecretKey extends Key
{
    public function secret(): static;
}
