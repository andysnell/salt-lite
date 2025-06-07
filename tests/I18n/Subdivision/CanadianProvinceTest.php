<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\I18n\Subdivision;

use PhoneBurner\SaltLite\I18n\IsoLocale;
use PhoneBurner\SaltLite\I18n\Region\Canada\Province;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionName;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Province::class)]
final class CanadianProvinceTest extends TestCase
{
    #[Test]
    public function enumCasesHaveCorrectValue(): void
    {
        self::assertSame('ON', Province::ON->value);
        self::assertSame('QC', Province::QC->value);
    }

    #[Test]
    public function labelReturnsCorrectSubdivisionName(): void
    {
        $province = Province::BC;
        $label = $province->label();

        self::assertInstanceOf(SubdivisionName::class, $label);
        self::assertSame('British Columbia', $label->value);
        self::assertSame(IsoLocale::EN_US, $label->locale); // Assuming default locale from attribute definition
    }

    #[Test]
    public function codeReturnsCorrectSubdivisionCode(): void
    {
        $province = Province::AB;
        $code = $province->code();

        self::assertInstanceOf(SubdivisionCode::class, $code);
        self::assertSame(SubdivisionCode::CA_AB, $code->value);
    }

    #[Test]
    public function getRegionReturnsCanada(): void
    {
        self::assertSame(Region::CA, Province::SK->getRegion());
    }

    #[Test]
    public function allCasesHaveLabelAndCode(): void
    {
        foreach (Province::cases() as $case) {
            self::assertInstanceOf(SubdivisionName::class, $case->label());
            self::assertInstanceOf(SubdivisionCode::class, $case->code());
            self::assertSame(Region::CA, $case->getRegion());
        }
    }
}
