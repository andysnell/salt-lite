<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\KeyManagement\Key;

interface PublicKey extends Key
{
    public function public(): static;
}
