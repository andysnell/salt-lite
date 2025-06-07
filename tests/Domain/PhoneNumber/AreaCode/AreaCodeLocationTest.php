<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode\AreaCodeLocation;
use PhoneBurner\SaltLite\I18n\Region\Canada\Province;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Region\UnitedStates\State;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AreaCodeLocationTest extends TestCase
{
    #[DataProvider('providesHappyPathTestCases')]
    #[Test]
    public function makeHappyPath(array $input, string $region, array $subdivisions): void
    {
        $sut = AreaCodeLocation::make(...$input);

        self::assertSame($region, $sut->region);
        self::assertSame($subdivisions, $sut->subdivisions);
        self::assertSame($sut, AreaCodeLocation::make(...$input));
        self::assertNull(AreaCodeLocation::make());
    }

    public static function providesHappyPathTestCases(): \Generator
    {
        yield [[AreaCodeLocation::NANP], AreaCodeLocation::NANP, []];

        yield [[Region::AI], Region::AI, []]; // "Anguilla",
        yield [[Region::AG], Region::AG, []]; // "Antigua & Barbuda",
        yield [[Region::BS], Region::BS, []]; // "Bahamas",
        yield [[Region::BB], Region::BB, []]; // "Barbados",
        yield [[Region::BM], Region::BM, []]; // "Bermuda",
        yield [[Region::VG], Region::VG, []]; // "British Virgin Islands",
        yield [[Region::CA], Region::CA, []]; // "Canada",
        yield [[Region::KY], Region::KY, []]; // "Cayman Islands",
        yield [[Region::DM], Region::DM, []]; // "Dominica",
        yield [[Region::DO], Region::DO, []]; // "Dominican Republic",
        yield [[Region::GD], Region::GD, []]; // "Grenada",
        yield [[Region::JM], Region::JM, []]; // "Jamaica",
        yield [[Region::MS], Region::MS, []]; // "Montserrat",
        yield [[Region::SX], Region::SX, []]; // "Sint Maarten",
        yield [[Region::KN], Region::KN, []]; // "St. Kitts & Nevis",
        yield [[Region::LC], Region::LC, []]; // "St. Lucia",
        yield [[Region::VC], Region::VC, []]; // "St. Vincent & Grenadines",
        yield [[Region::TT], Region::TT, []]; // "Trinidad & Tobago",
        yield [[Region::TC], Region::TC, []]; // "Turks & Caicos Islands",
        yield [[Region::US], Region::US, []]; // "United States",

        // Passing the same region twice is ok.
        yield [[Region::US, Region::US], Region::US, []];
        yield [[AreaCodeLocation::NANP, AreaCodeLocation::NANP, AreaCodeLocation::NANP], AreaCodeLocation::NANP, []];

        yield [[State::MO], Region::US, [State::MO->value => State::MO]];
        yield [[State::MO, Region::US], Region::US, [State::MO->value => State::MO]];
        yield [[Region::US, State::MO,], Region::US, [State::MO->value => State::MO]];
        yield [[State::MO, State::MO,], Region::US, [State::MO->value => State::MO]];

        yield [
            [State::MO, State::OH, State::MO],
            Region::US,
            [
                State::MO->value => State::MO,
                State::OH->value => State::OH,
            ],
        ];

        yield [
            [State::OH, State::MO],
            Region::US,
            [
                State::MO->value => State::MO,
                State::OH->value => State::OH,
            ],
        ];

        yield [[SubdivisionCode::CA_NL], Region::CA, [Province::NL->value => SubdivisionCode::CA_NL]];

        yield [
            [SubdivisionCode::CA_NS, SubdivisionCode::CA_PE],
            Region::CA,
            [
                SubdivisionCode::CA_NS => SubdivisionCode::CA_NS,
                SubdivisionCode::CA_PE => SubdivisionCode::CA_PE,
            ],
        ];

        yield [[Region::AS], Region::US, [State::AS->value => State::AS]];
        yield [[Region::GU], Region::US, [State::GU->value => State::GU]];
        yield [[Region::MP], Region::US, [State::MP->value => State::MP]];
        yield [[Region::PR], Region::US, [State::PR->value => State::PR]];
        yield [[Region::VI], Region::US, [State::VI->value => State::VI]];

        yield [[State::AS], Region::US, [State::AS->value => State::AS]];
        yield [[State::GU], Region::US, [State::GU->value => State::GU]];
        yield [[State::MP], Region::US, [State::MP->value => State::MP]];
        yield [[State::PR], Region::US, [State::PR->value => State::PR]];
        yield [[State::VI], Region::US, [State::VI->value => State::VI]];

        // Usually passing more than one region code would result in an exception
        // being thrown; however, US Territories are a special case and are
        // cast to their subdivision equivalent.
        yield [
            [
                Region::AS,
                Region::GU,
                Region::MP,
                Region::PR,
                Region::VI,
            ],
            Region::US,
            [
                State::AS->value => State::AS,
                State::GU->value => State::GU,
                State::MP->value => State::MP,
                State::PR->value => State::PR,
                State::VI->value => State::VI,
            ],
        ];
    }

    #[DataProvider('providesDifferentRegionSadPathTestCases')]
    #[Test]
    public function passingTwoDifferentRegionsFails(array $input): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('AreaCodeLocation Requires 1 Region');
        AreaCodeLocation::make(...$input);
    }

    public static function providesDifferentRegionSadPathTestCases(): \Generator
    {
        yield [[Region::US, Region::CA]];
        yield [[State::CA, SubdivisionCode::CA_ON]];
        yield [[State::CA, Region::CA]];
    }

    #[Test]
    public function passingInvalidRegionCodeFails(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Invalid NANP Region Code: MK');
        AreaCodeLocation::make(Region::MK);
    }

//    #[Test]
//    public function passingInvalidSubdivisionCodeFails(): void
//    {
//        $this->expectException(\UnexpectedValueException::class);
//        $this->expectExceptionMessage('Undefined Subdivision Code: US-PE');
//        /** @phpstan-ignore-next-line intentional defect */
//        AreaCodeLocation::make('US-PE');
//    }
}
