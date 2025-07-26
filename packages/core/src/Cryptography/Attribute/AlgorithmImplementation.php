<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Attribute;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricEncryptionAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SymmetricEncryptionAlgorithm;

#[\Attribute]
final readonly class AlgorithmImplementation
{
    public function __construct(public AsymmetricEncryptionAlgorithm|SymmetricEncryptionAlgorithm $algorithm)
    {
    }
}
