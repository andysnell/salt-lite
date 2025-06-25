<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Domain;

use PhoneBurner\SaltLite\Time\DateTimeAware;

enum MonthOfYear: int
{
    case January = 1;
    case February = 2;
    case March = 3;
    case April = 4;
    case May = 5;
    case June = 6;
    case July = 7;
    case August = 8;
    case September = 9;
    case October = 10;
    case November = 11;
    case December = 12;

    public static function instance(mixed $value): self
    {
        return self::parse($value) ?? throw new \UnexpectedValueException("Invalid month value");
    }

    public static function parse(mixed $value): self|null
    {
        return match (true) {
            $value instanceof self, $value === null => $value,
            $value instanceof \DateTimeInterface => self::{$value->format('F')},
            $value instanceof DateTimeAware => self::{$value->getDateTime()->format('F')},
            \is_numeric($value) => self::tryFrom((int)$value),
            \is_string($value) => match (\strtolower(\substr($value, 0, 3))) {
                'jan' => self::January,
                'feb' => self::February,
                'mar' => self::March,
                'apr' => self::April,
                'may' => self::May,
                'jun' => self::June,
                'jul' => self::July,
                'aug' => self::August,
                'sep' => self::September,
                'oct' => self::October,
                'nov' => self::November,
                'dec' => self::December,
                default => null,
            },
            default => null,
        };
    }
}
