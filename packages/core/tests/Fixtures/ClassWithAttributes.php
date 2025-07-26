<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use PhoneBurner\SaltLite\Tests\Fixtures\Attributes\MockAttribute;

#[MockAttribute('class')]
class ClassWithAttributes
{
    public const string CONSTANT = 'constant';

    #[MockAttribute('property')]
    public string $property = '';

    #[MockAttribute('method')]
    public function method(): void
    {
    }
}
