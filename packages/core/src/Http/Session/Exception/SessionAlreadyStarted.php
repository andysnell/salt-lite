<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Session\Exception;

class SessionAlreadyStarted extends \LogicException implements HttpSessionException
{
}
