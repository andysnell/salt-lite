<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time;

use PhoneBurner\SaltLite\Attribute\Usage\Internal;

/**
 * Note: we're following the example of the ISO 8601 standard and treating
 * "days" as a fixed time unit equal to exactly 24 hours (or 86,400 seconds).
 * However, if considered in a "calendar" context, a day may have variable
 * length due to daylight savings time or leap seconds (pre-2035).
 *
 * While the second may be the base unit of time in the SI/metric system,
 * we will generally treat microseconds as the base unit for measuring time.
 * This aligns with how time is represented most of the built-in PHP objects/functions,
 * excluding high-resolution timing functions that operate on nanoseconds. It also
 * aligns with the "fractional seconds" component of ISO 8601 datetime and duration
 * strings, which allow up to six decimal places. Thus, for the most part, we can
 * ignore the nanosecond and millisecond as distinct cases and just handle the
 * conversion where necessary. There is a technical lost of precision with this approach;
 * however, considering light takes over 5 microseconds to travel a mile in a
 * vacuum, it's not significant by any means.
 *
 * This enum also defines public constants (in the PHP sense) for time-related
 * constants (in the scientific, "Plank's Constant", pi, or e sense).
 */
#[Internal]
enum TimeUnit
{
    case Year;
    case Month;
    case Week;
    case Day;
    case Hour;
    case Minute;
    case Second;
    case Millisecond;
    case Microsecond;
    case Nanosecond;

    public const int NANOSECONDS_IN_MICROSECOND = 1000;
    public const int NANOSECONDS_IN_MILLISECOND = 1_000_000;
    public const int NANOSECONDS_IN_SECOND = 1_000_000_000;
    public const int NANOSECONDS_IN_MINUTE = 60_000_000_000;
    public const int NANOSECONDS_IN_HOUR = 3_600_000_000_000;
    public const int NANOSECONDS_IN_DAY = 86_400_000_000_000;
    public const int NANOSECONDS_IN_WEEK = 604_800_000_000_000;
    public const int NANOSECONDS_IN_MONTH_MIN = 2_419_200_000_000_000;
    public const int NANOSECONDS_IN_MONTH_MAX = 2_678_400_000_000_000;
    public const int NANOSECONDS_IN_YEAR_MIN = 31_536_000_000_000_000;
    public const int NANOSECONDS_IN_YEAR_MAX = 31_622_400_000_000_000;

    public const int MICROSECONDS_IN_MILLISECOND = 1000;
    public const int MICROSECONDS_IN_SECOND = 1_000_000;
    public const int MICROSECONDS_IN_MINUTE = 60_000_000;
    public const int MICROSECONDS_IN_HOUR = 3_600_000_000;
    public const int MICROSECONDS_IN_DAY = 86_400_000_000;
    public const int MICROSECONDS_IN_WEEK = 604_800_000_000;
    public const int MICROSECONDS_IN_MONTH_MIN = 2_419_200_000_000;
    public const int MICROSECONDS_IN_MONTH_MAX = 2_678_400_000_000;
    public const int MICROSECONDS_IN_YEAR_MIN = 31_536_000_000_000;
    public const int MICROSECONDS_IN_YEAR_MAX = 31_622_400_000_000;

    public const int MILLISECONDS_IN_SECOND = 1000;
    public const int MILLISECONDS_IN_MINUTE = 60_000;
    public const int MILLISECONDS_IN_HOUR = 3_600_000;
    public const int MILLISECONDS_IN_DAY = 86_400_000;
    public const int MILLISECONDS_IN_WEEK = 604_800_000;
    public const int MILLISECONDS_IN_MONTH_MIN = 2_419_200_000;
    public const int MILLISECONDS_IN_MONTH_MAX = 2_678_400_000;
    public const int MILLISECONDS_IN_YEAR_MIN = 31_536_000_000;

    public const int SECONDS_IN_MINUTE = 60;
    public const int SECONDS_IN_HOUR = 3600;
    public const int SECONDS_IN_DAY = 86_400;
    public const int SECONDS_IN_WEEK = 604_800;
    public const int SECONDS_IN_MONTH_MIN = 2_419_200;
    public const int SECONDS_IN_MONTH_MAX = 2_678_400;
    public const int SECONDS_IN_YEAR_MIN = 31_536_000;
    public const int SECONDS_IN_YEAR_MAX = 31_622_400;

    public const int MINUTES_IN_HOUR = 60;
    public const int MINUTES_IN_DAY = 1440;
    public const int MINUTES_IN_WEEK = 10_080;
    public const int MINUTES_IN_MONTH_MIN = 40_320;
    public const int MINUTES_IN_MONTH_MAX = 44_640;
    public const int MINUTES_IN_YEAR_MIN = 525_600;
    public const int MINUTES_IN_YEAR_MAX = 527_040;

    public const int HOURS_IN_DAY = 24;
    public const int HOURS_IN_WEEK = 168;
    public const int HOURS_IN_MONTH_MIN = 672;
    public const int HOURS_IN_MONTH_MAX = 744;
    public const int HOURS_IN_YEAR_MIN = 8760;
    public const int HOURS_IN_YEAR_MAX = 8784;

    public const int DAYS_IN_WEEK = 7;
    public const int DAYS_IN_MONTH_MIN = 28;
    public const int DAYS_IN_MONTH_MAX = 31;
    public const int DAYS_IN_YEAR_MIN = 365;
    public const int DAYS_IN_YEAR_MAX = 366;

    public const int WEEKS_IN_YEAR_MAX = 52;
    public const int WEEKS_IN_YEAR_MIN = 53;

    public const int MONTHS_IN_YEAR_MAX = 12;

    public function isFixedLengthUnit(): bool
    {
        return $this !== self::Year && $this !== self::Month;
    }
}
