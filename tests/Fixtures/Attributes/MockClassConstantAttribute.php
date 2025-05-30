<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
final readonly class MockClassConstantAttribute
{
    public function __construct(public string $value)
    {
    }
}
