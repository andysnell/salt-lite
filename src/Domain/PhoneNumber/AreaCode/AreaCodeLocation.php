<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\I18n\Region\Canada\Province;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Region\Subdivision;
use PhoneBurner\SaltLite\I18n\Region\UnitedStates\State;
use UnexpectedValueException;

/**
 * Collection of ISO3166-1 alpha-2 and ISO 3166-2 country/subdivision code strings
 * Also uses "NANP" to indicate an area code that is associated with all the
 * NANP regions, for example, a toll-free, or unassigned number.
 */
#[Contract]
final readonly class AreaCodeLocation
{
    public const string NANP = 'NANP';

    /**
     * United States Territories are listed as regions in the list of
     * NANP region codes for wider compatiblity, including the NANP database.
     *
     * @var array<value-of<Region>, Region>
     */
    public const array NANP_REGIONS = [
        Region::AI->value => Region::AI, // "Anguilla",
        Region::AG->value => Region::AG, // "Antigua & Barbuda",
        Region::BS->value => Region::BS, // "Bahamas",
        Region::BB->value => Region::BB, // "Barbados",
        Region::BM->value => Region::BM, // "Bermuda",
        Region::VG->value => Region::VG, // "British Virgin Islands",
        Region::CA->value => Region::CA, // "Canada",
        Region::KY->value => Region::KY, // "Cayman Islands",
        Region::DM->value => Region::DM, // "Dominica",
        Region::DO->value => Region::DO, // "Dominican Republic",
        Region::GD->value => Region::GD, // "Grenada",
        Region::JM->value => Region::JM, // "Jamaica",
        Region::MS->value => Region::MS, // "Montserrat",
        Region::SX->value => Region::SX, // "Sint Maarten",
        Region::KN->value => Region::KN, // "St. Kitts & Nevis",
        Region::LC->value => Region::LC, // "St. Lucia",
        Region::VC->value => Region::VC, // "St. Vincent & Grenadines",
        Region::TT->value => Region::TT, // "Trinidad & Tobago",
        Region::TC->value => Region::TC, // "Turks & Caicos Islands",
        Region::US->value => Region::US, // "United States",
        Region::VI->value => Region::VI, // "U.S. Virgin Islands",
        Region::AS->value => Region::AS, // "American Samoa",
        Region::PR->value => Region::PR, // "Puerto Rico",
        Region::GU->value => Region::GU, // "Guam",
        Region::MP->value => Region::MP, // "Northern Mariana Islands",
    ];

    private const array UNITED_STATES_TERRITORIES = [
        Region::AS->name => State::AS, // "American Samoa",
        Region::GU->name => State::GU, // "Guam",
        Region::MP->name => State::MP, // "Northern Mariana Islands",
        Region::PR->name => State::PR, // "Puerto Rico",
        Region::VI->name => State::VI, // "U.S. Virgin Islands",
    ];

    public Region $region;

    /**
     * @var array<State|Province>
     */
    public array $subdivisions;

    private function __construct(Region|State|Province ...$codes)
    {
        $regions = [];
        $subdivisions = [];
        foreach ($codes as $code) {
            // Cast US Territories to Regions for compatiblity with NANP definitions
            if ($code instanceof State && \array_key_exists($code->name, self::UNITED_STATES_TERRITORIES)) {
                $code = Region::{$code->name};
            }

            $region = $code->getRegion();
            $regions[$region->name] = $code->getRegion();
            if ($code instanceof Subdivision) {
                $subdivisions[$code->name] = $code;
            }
        }

        if (\count($regions) !== 1) {
            throw new \InvalidArgumentException('AreaCodeLocation Requires Exactly 1 Region');
        }

        $this->region = \reset($regions);
        if (! \array_key_exists($this->region->value, self::NANP_REGIONS)) {
            throw new UnexpectedValueException('Invalid NANP Region: ' . $this->region->name);
        }

        $this->subdivisions = $subdivisions;
        foreach ($this->subdivisions as $subdivision) {
            if ($subdivision->getRegion() !== $this->region) {
                throw new UnexpectedValueException('AreaCodeLocation Subdivision Region Mismatch');
            }
        }
    }

    public static function make(Region|State|Province ...$codes): self|null
    {
        static $cache = [];
        static $format = static fn(\UnitEnum $code): string => $code::class . '::' . $code->name;

        if ($codes === []) {
            return null; // non-geographic area code
        }

        \sort($codes);

        return $cache[\implode('&', \array_map($format, $codes))] ??= new self(...$codes);
    }
}
