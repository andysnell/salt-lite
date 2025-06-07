<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\I18n\Region\Canada;

use PhoneBurner\SaltLite\Enum\EnumCaseAttr;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Region\RegionAware;
use PhoneBurner\SaltLite\I18n\Region\Subdivision;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionName;

enum Province: string implements RegionAware, Subdivision
{
    #[SubdivisionName('Alberta')]
    #[SubdivisionCode(SubdivisionCode::CA_AB)]
    case AB = 'AB';

    #[SubdivisionName('British Columbia')]
    #[SubdivisionCode(SubdivisionCode::CA_BC)]
    case BC = 'BC';

    #[SubdivisionName('Manitoba')]
    #[SubdivisionCode(SubdivisionCode::CA_MB)]
    case MB = 'MB';

    #[SubdivisionName('New Brunswick')]
    #[SubdivisionCode(SubdivisionCode::CA_NB)]
    case NB = 'NB';

    #[SubdivisionName('Newfoundland and Labrador')]
    #[SubdivisionCode(SubdivisionCode::CA_NL)]
    case NL = 'NL';

    #[SubdivisionName('Nova Scotia')]
    #[SubdivisionCode(SubdivisionCode::CA_NS)]
    case NS = 'NS';

    #[SubdivisionName('Northwest Territories')]
    #[SubdivisionCode(SubdivisionCode::CA_NT)]
    case NT = 'NT';

    #[SubdivisionName('Nunavut')]
    #[SubdivisionCode(SubdivisionCode::CA_NU)]
    case NU = 'NU';

    #[SubdivisionName('Ontario')]
    #[SubdivisionCode(SubdivisionCode::CA_ON)]
    case ON = 'ON';

    #[SubdivisionName('Prince Edward Island')]
    #[SubdivisionCode(SubdivisionCode::CA_PE)]
    case PE = 'PE';

    #[SubdivisionName('Quebec')]
    #[SubdivisionCode(SubdivisionCode::CA_QC)]
    case QC = 'QC';

    #[SubdivisionName('Saskatchewan')]
    #[SubdivisionCode(SubdivisionCode::CA_SK)]
    case SK = 'SK';

    #[SubdivisionName('Yukon')]
    #[SubdivisionCode(SubdivisionCode::CA_YT)]
    case YT = 'YT';

    public function label(): SubdivisionName
    {
        static $cache = new \SplObjectStorage();
        return $cache[$this] ??= EnumCaseAttr::fetch($this, SubdivisionName::class);
    }

    public function code(): SubdivisionCode
    {
        static $cache = new \SplObjectStorage();
        return $cache[$this] ??= EnumCaseAttr::fetch($this, SubdivisionCode::class);
    }

    public function getRegion(): Region
    {
        return Region::CA;
    }
}
