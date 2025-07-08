<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time;

use PhoneBurner\SaltLite\String\Str;
use PhoneBurner\SaltLite\Time\TimeUnit as T;

final readonly class Duration implements \Stringable
{
    /**
     * | Group | Meaning | Example value |
     * | ----- | --------------- | ---------------- |
     * | 1 | Weeks only | `4` in `P4W` |
     * | 2 | Years | `2` in `P2Y` |
     * | 3 | Months | `3` in `P3M` |
     * | 4 | Days | `5` in `P5D` |
     * | 5 | Hours | `6` in `PT6H` |
     * | 6 | Minutes | `7` in `PT7M` |
     * | 7 | Seconds integer | `8` in `PT8.5S` |
     * | 8 | Fractional part | `5` in `PT8.5S` |
     */
    // phpcs:ignore Generic.Files.LineLength
    public const string REGEXP = '/^P(?:(\d+)W|(?:(\d+)Y)?(?:(\d+)M)?(?:(\d+)D)?(?:T(?:(\d+)H)?(?:(\d+)M)?(?:(\d+)(?:\.(\d{1,6}))?S)?)?)$/';

    public const array UNITS = ['years', 'months', 'weeks', 'days', 'hours', 'minutes', 'seconds', 'microseconds'];

    public const string EMPTY_DURATION = 'PT0S';

    public function __construct(
        public int $years = 0,
        public int $months = 0,
        public int $weeks = 0,
        public int $days = 0,
        public int $hours = 0,
        public int $minutes = 0,
        public int $seconds = 0,
        public int $microseconds = 0,
    ) {
        $checksum = \array_reduce(
            self::UNITS,
            function ($carry, $unit): int {
                $this->$unit >= 0 || throw new \UnexpectedValueException($unit . ' must be greater than or equal to 0');
                return $carry + $this->$unit;
            },
            0,
        );

        if ($weeks !== 0 && $this->weeks !== $checksum) {
            throw new \UnexpectedValueException('invalid duration: cannot mix weeks and other units');
        }

        if ($this->microseconds >= T::MICROSECONDS_IN_SECOND) {
            throw new \OverflowException('invalid duration: cannot overflow fractional seconds');
        }
    }

    public static function make(\DateInterval $interval): self
    {
        return new self(
            years: $interval->y,
            months: $interval->m,
            days: $interval->d,
            hours: $interval->h,
            minutes: $interval->i,
            seconds: $interval->s,
            microseconds: $interval->f ? (int)($interval->f * T::MICROSECONDS_IN_SECOND) : 0,
        );
    }

    public static function instance(\DateInterval|\Stringable|self|string $duration): self
    {
        return self::parse($duration) ?? throw new \UnexpectedValueException('Invalid Duration');
    }

    public static function parse(\DateInterval|\Stringable|self|string $duration): self|null
    {
        try {
            return match (true) {
                $duration instanceof self => $duration,
                $duration instanceof \DateInterval => self::make($duration),
                ! \preg_match(self::REGEXP, \strtoupper((string)$duration), $matches) => null,
                (int)($matches[1]) !== 0 => new self(weeks: (int)$matches[1]),
                default => new self(
                    years: (int)($matches[2] ?? 0),
                    months: (int)($matches[3] ?? 0),
                    days: (int)($matches[4] ?? 0),
                    hours: (int)($matches[5] ?? 0),
                    minutes: (int)($matches[6] ?? 0),
                    seconds: (int)($matches[7] ?? 0),
                    microseconds: isset($matches[8]) ? (int)Str::rpad($matches[8], 6, '0') : 0,
                ),
            };
        } catch (\Throwable) {
            return null;
        }
    }

    public function __toString(): string
    {
        if ($this->weeks !== 0) {
            return \sprintf('P%dW', $this->weeks);
        }

        $date_units = \implode('', \array_filter([
            $this->years ? $this->years . 'Y' : null,
            $this->months ? $this->months . 'M' : null,
            $this->days ? $this->days . 'D' : null,
        ]));

        $time_units = \implode('', \array_filter([
            $this->hours ? $this->hours . 'H' : null,
            $this->minutes ? $this->minutes . 'M' : null,
            match (true) {
                $this->microseconds !== 0 => \rtrim(\sprintf(
                    '%d.%06d',
                    $this->seconds,
                    $this->microseconds,
                ), '0') . 'S',
                $this->seconds !== 0 => $this->seconds . 'S',
                default => null,
            },
        ]));

        if ($date_units === '' && $time_units === '') {
            return self::EMPTY_DURATION;
        }

        return \sprintf('P%s%s%s', $date_units, $time_units ? 'T' : '', $time_units);
    }
}
