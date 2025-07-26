<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Exception;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;

/**
 * This "ShouldNotHappen" exception is thrown when code that should be unreachable
 * is reached, and is used to guard against logic errors that otherwise trigger
 * errors in static analysis tooling.
 */
#[Contract]
final class UnreachableCode extends \LogicException
{
}
