<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\I18n;

use PhoneBurner\SaltLite\I18n\Currency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Currency::class)]
final class CurrencyTest extends TestCase
{
    #[Test]
    public function enumCasesExistAndHaveCorrectValues(): void
    {
        // Test a few sample cases
        self::assertSame('USD', Currency::USD->value);
        self::assertSame('EUR', Currency::EUR->value);
        self::assertSame('CAD', Currency::CAD->value);
        self::assertCount(166, Currency::cases()); // Update count to actual 166
    }
}
