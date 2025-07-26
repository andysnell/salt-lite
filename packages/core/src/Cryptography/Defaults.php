<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SymmetricAlgorithm;

class Defaults
{
    public function __construct(
        public SymmetricAlgorithm $symmetric = SymmetricAlgorithm::Aegis256,
        public AsymmetricAlgorithm $asymmetric = AsymmetricAlgorithm::X25519Aegis256,
    ) {
    }
}
