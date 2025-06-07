<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\I18n\Region\UnitedStates;

use PhoneBurner\SaltLite\Enum\EnumCaseAttr;
use PhoneBurner\SaltLite\I18n\Region\Region;
use PhoneBurner\SaltLite\I18n\Region\RegionAware;
use PhoneBurner\SaltLite\I18n\Region\Subdivision;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionName;

/**
 * United States: 50 States, 1 Federal District, and 6 Territories
 *
 * Note: The six territories have both ISO 3661-1 Alpha 2 and CLDR entries
 * as "country level" regions and ISO 3661-2 entries as political subdivisions
 * of the United States. E.g. Guam is both "GU" and "US-GU".
 */
enum State: string implements RegionAware, Subdivision
{
    #[SubdivisionName('Alabama')]
    #[SubdivisionCode('US-AL')]
    case AL = 'AL';

    #[SubdivisionName('Alaska')]
    #[SubdivisionCode('US-AK')]
    case AK = 'AK';

    #[SubdivisionName('Arizona')]
    #[SubdivisionCode('US-AZ')]
    case AZ = 'AZ';

    #[SubdivisionName('Arkansas')]
    #[SubdivisionCode('US-AR')]
    case AR = 'AR';

    #[SubdivisionName('California')]
    #[SubdivisionCode('US-CA')]
    case CA = 'CA';

    #[SubdivisionName('Colorado')]
    #[SubdivisionCode('US-CO')]
    case CO = 'CO';

    #[SubdivisionName('Connecticut')]
    #[SubdivisionCode('US-CT')]
    case CT = 'CT';

    #[SubdivisionName('Delaware')]
    #[SubdivisionCode('US-DE')]
    case DE = 'DE';

    #[SubdivisionName('Florida')]
    #[SubdivisionCode('US-FL')]
    case FL = 'FL';

    #[SubdivisionName('Georgia')]
    #[SubdivisionCode('US-GA')]
    case GA = 'GA';

    #[SubdivisionName('Hawaii')]
    #[SubdivisionCode('US-HI')]
    case HI = 'HI';

    #[SubdivisionName('Idaho')]
    #[SubdivisionCode('US-ID')]
    case ID = 'ID';

    #[SubdivisionName('Illinois')]
    #[SubdivisionCode('US-IL')]
    case IL = 'IL';

    #[SubdivisionName('Indiana')]
    #[SubdivisionCode('US-IN')]
    case IN = 'IN';

    #[SubdivisionName('Iowa')]
    #[SubdivisionCode('US-IA')]
    case IA = 'IA';

    #[SubdivisionName('Kansas')]
    #[SubdivisionCode('US-KS')]
    case KS = 'KS';

    #[SubdivisionName('Kentucky')]
    #[SubdivisionCode('US-KY')]
    case KY = 'KY';

    #[SubdivisionName('Louisiana')]
    #[SubdivisionCode('US-LA')]
    case LA = 'LA';

    #[SubdivisionName('Maine')]
    #[SubdivisionCode('US-ME')]
    case ME = 'ME';

    #[SubdivisionName('Maryland')]
    #[SubdivisionCode('US-MD')]
    case MD = 'MD';

    #[SubdivisionName('Massachusetts')]
    #[SubdivisionCode('US-MA')]
    case MA = 'MA';

    #[SubdivisionName('Michigan')]
    #[SubdivisionCode('US-MI')]
    case MI = 'MI';

    #[SubdivisionName('Minnesota')]
    #[SubdivisionCode('US-MN')]
    case MN = 'MN';

    #[SubdivisionName('Mississippi')]
    #[SubdivisionCode('US-MS')]
    case MS = 'MS';

    #[SubdivisionName('Missouri')]
    #[SubdivisionCode('US-MO')]
    case MO = 'MO';

    #[SubdivisionName('Montana')]
    #[SubdivisionCode('US-MT')]
    case MT = 'MT';

    #[SubdivisionName('Nebraska')]
    #[SubdivisionCode('US-NE')]
    case NE = 'NE';

    #[SubdivisionName('Nevada')]
    #[SubdivisionCode('US-NV')]
    case NV = 'NV';

    #[SubdivisionName('New Hampshire')]
    #[SubdivisionCode('US-NH')]
    case NH = 'NH';

    #[SubdivisionName('New Jersey')]
    #[SubdivisionCode('US-NJ')]
    case NJ = 'NJ';

    #[SubdivisionName('New Mexico')]
    #[SubdivisionCode('US-NM')]
    case NM = 'NM';

    #[SubdivisionName('New York')]
    #[SubdivisionCode('US-NY')]
    case NY = 'NY';

    #[SubdivisionName('North Carolina')]
    #[SubdivisionCode('US-NC')]
    case NC = 'NC';

    #[SubdivisionName('North Dakota')]
    #[SubdivisionCode('US-ND')]
    case ND = 'ND';

    #[SubdivisionName('Ohio')]
    #[SubdivisionCode('US-OH')]
    case OH = 'OH';

    #[SubdivisionName('Oklahoma')]
    #[SubdivisionCode('US-OK')]
    case OK = 'OK';

    #[SubdivisionName('Oregon')]
    #[SubdivisionCode('US-OR')]
    case OR = 'OR';

    #[SubdivisionName('Pennsylvania')]
    #[SubdivisionCode('US-PA')]
    case PA = 'PA';

    #[SubdivisionName('Rhode Island')]
    #[SubdivisionCode('US-RI')]
    case RI = 'RI';

    #[SubdivisionName('South Carolina')]
    #[SubdivisionCode('US-SC')]
    case SC = 'SC';

    #[SubdivisionName('South Dakota')]
    #[SubdivisionCode('US-SD')]
    case SD = 'SD';

    #[SubdivisionName('Tennessee')]
    #[SubdivisionCode('US-TN')]
    case TN = 'TN';

    #[SubdivisionName('Texas')]
    #[SubdivisionCode('US-TX')]
    case TX = 'TX';

    #[SubdivisionName('Utah')]
    #[SubdivisionCode('US-UT')]
    case UT = 'UT';

    #[SubdivisionName('Vermont')]
    #[SubdivisionCode('US-VT')]
    case VT = 'VT';

    #[SubdivisionName('Virginia')]
    #[SubdivisionCode('US-VA')]
    case VA = 'VA';

    #[SubdivisionName('Washington')]
    #[SubdivisionCode('US-WA')]
    case WA = 'WA';

    #[SubdivisionName('West Virginia')]
    #[SubdivisionCode('US-WV')]
    case WV = 'WV';

    #[SubdivisionName('Wisconsin')]
    #[SubdivisionCode('US-WI')]
    case WI = 'WI';

    #[SubdivisionName('Wyoming')]
    #[SubdivisionCode('US-WY')]
    case WY = 'WY';

    #[SubdivisionName('District of Columbia')]
    #[SubdivisionCode('US-DC')]
    case DC = 'DC';

    #[SubdivisionName('American Samoa')]
    #[SubdivisionCode('US-AS')]
    case AS = 'AS';

    #[SubdivisionName('Guam')]
    #[SubdivisionCode('US-GU')]
    case GU = 'GU';

    #[SubdivisionName('Northern Mariana Islands')]
    #[SubdivisionCode('US-MP')]
    case MP = 'MP';

    #[SubdivisionName('Puerto Rico')]
    #[SubdivisionCode('US-PR')]
    case PR = 'PR';

    #[SubdivisionName('US Virgin Islands')]
    #[SubdivisionCode('US-VI')]
    case VI = 'VI';

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
        return Region::US;
    }
}
