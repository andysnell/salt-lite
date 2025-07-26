<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App\Exception;

use LogicException;

class KernelError extends LogicException implements BootError
{
}
