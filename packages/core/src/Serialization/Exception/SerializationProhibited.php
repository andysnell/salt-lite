<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Serialization\Exception;

use PhoneBurner\SaltLite\Cryptography\Exception\CryptoException;

class SerializationProhibited extends \LogicException implements CryptoException
{
    public function __construct()
    {
        parent::__construct('Serialization of Objects with Sensitive Parameters is Prohibited');
    }
}
