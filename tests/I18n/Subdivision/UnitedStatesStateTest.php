<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\I18n\Subdivision;

use PhoneBurner\SaltLite\I18n\IsoLocale;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionName;
use PhoneBurner\SaltLite\I18n\Subdivision\UnitedStatesState;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnitedStatesState::class)]
final class UnitedStatesStateTest extends TestCase
{
    #[Test]
    public function enumCasesHaveCorrectValue(): void
    {
        self::assertSame('CA', UnitedStatesState::CA->value);
        self::assertSame('TX', UnitedStatesState::TX->value);
    }

    #[Test]
    public function labelReturnsCorrectSubdivisionName(): void
    {
        $state = UnitedStatesState::NY;
        $label = $state->label();

        self::assertInstanceOf(SubdivisionName::class, $label);
        self::assertSame('New York', $label->value);
        self::assertSame(IsoLocale::EN_US, $label->locale); // Assuming default locale from attribute definition
    }

    #[Test]
    public function codeReturnsCorrectSubdivisionCode(): void
    {
        $state = UnitedStatesState::FL;
        $code = $state->code();

        self::assertInstanceOf(SubdivisionCode::class, $code);
        self::assertSame(SubdivisionCode::US_FL, $code->value);
    }

    #[Test]
    public function getRegionReturnsUsa(): void
    {
        self::assertSame(Region::US, UnitedStatesState::WA->getRegion());
    }

    #[Test]
    public function allCasesHaveLabelAndCode(): void
    {
        foreach (UnitedStatesState::cases() as $case) {
            self::assertInstanceOf(SubdivisionName::class, $case->label());
            self::assertInstanceOf(SubdivisionCode::class, $case->code());
            self::assertSame(Region::US, $case->getRegion());
        }
    }
}
