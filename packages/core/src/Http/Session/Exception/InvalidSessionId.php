<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Session\Exception;

class InvalidSessionId extends \UnexpectedValueException implements HttpSessionException
{
}
