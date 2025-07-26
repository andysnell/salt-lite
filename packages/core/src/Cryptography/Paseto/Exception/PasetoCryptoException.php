<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Exception;

use PhoneBurner\SaltLite\Cryptography\Exception\CryptoRuntimeException;

class PasetoCryptoException extends CryptoRuntimeException implements PasetoException
{
}
