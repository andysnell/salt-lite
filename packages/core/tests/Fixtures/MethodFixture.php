<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use Psr\Log\LoggerInterface;

class MethodFixture
{
    public function methodWithParameters(mixed $first, mixed $second): void
    {
    }

    public function methodWithTypeHint(LoggerInterface $logger): void
    {
    }

    public function methodWithDefaultValue(mixed $param = 'default'): void
    {
    }

    public function methodWithDefaultAndType(LoggerInterface|null $logger = null): void
    {
    }

    public function methodWithUnionType(string|int $param): void
    {
    }

    public function methodWithBuiltinType(string $param): void
    {
    }

    public function methodWithSelfType(self $param): void
    {
    }
}
