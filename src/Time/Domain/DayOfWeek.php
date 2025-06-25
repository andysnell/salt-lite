<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\Domain;

use PhoneBurner\SaltLite\Time\DateTimeAware;

enum DayOfWeek: int implements DayOfWeekAware
{
    case Sunday = 0;
    case Monday = 1;
    case Tuesday = 2;
    case Wednesday = 3;
    case Thursday = 4;
    case Friday = 5;
    case Saturday = 6;

    public static function instance(mixed $value): self
    {
        return self::parse($value) ?? throw new \UnexpectedValueException(
            'Invalid value for DayOfWeek',
        );
    }

    public static function parse(mixed $value): self|null
    {
        return match (true) {
            $value instanceof self, $value === null => $value,
            $value instanceof \DateTimeInterface => self::{$value->format('l')},
            $value instanceof DateTimeAware => self::{$value->getDateTime()->format('l')},
            \is_numeric($value) => self::tryFrom((int)$value),
            \is_string($value) => match (\strtolower(\substr($value, 0, 3))) {
                'sun' => self::Sunday,
                'mon' => self::Monday,
                'tue' => self::Tuesday,
                'wed' => self::Wednesday,
                'thu' => self::Thursday,
                'fri' => self::Friday,
                'sat' => self::Saturday,
                default => null,
            },
            default => null,
        };
    }

    public function getIso8601DayOfWeek(): int
    {
        return $this->value ?: 7; // ISO 8601 considers Monday as 1 and Sunday as 7
    }

    public function getDayOfWeek(): self
    {
        return $this;
    }
}
