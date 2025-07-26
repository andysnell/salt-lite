<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFound extends \LogicException implements NotFoundExceptionInterface
{
}
