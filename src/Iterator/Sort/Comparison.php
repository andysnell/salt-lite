<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator\Sort;

/**
 * An enumeration of the various ways that PHP can compare
 * values when sorting an array. The value is the integer
 * flag that would be passed into one of the builtin functions
 * like sort() or krsort() to determine the comparison method.
 */
enum Comparison: int
{
    case Regular = \SORT_REGULAR;
    case String = \SORT_STRING;
    case StringCaseInsensitive = \SORT_STRING | \SORT_FLAG_CASE;
    case Natural = \SORT_NATURAL;
    case NaturalCaseInsensitive = \SORT_NATURAL | \SORT_FLAG_CASE;
    case Numeric = \SORT_NUMERIC;
    case Locale = \SORT_LOCALE_STRING;
}
