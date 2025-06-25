<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\DateTimeAware;
use PhoneBurner\SaltLite\Time\Domain\DayOfWeek;
use PhoneBurner\SaltLite\Time\Domain\DayOfWeekAware;

class Date implements
    LocalTimeConstruct,
    YearAware,
    MonthAware,
    DateAware,
    DayOfWeekAware
{
    public readonly Month $month;
    public readonly int $day;

    public function __construct(\DateTimeInterface $date)
    {
        $this->month = Month::make($date);
        $this->day = (int)$date->format('j');
    }

    public static function make(
        \DateTimeInterface|DateTimeAware|string|int|null $date = null,
        Clock $clock = new SystemClock(),
    ): self {
        return match (true) {
            $date instanceof \DateTimeInterface => new self($date),
            $date instanceof DateTimeAware => new self($date->getDateTime()),
            $date === null => self::current($clock),
            default => self::parseFromString((string)$date),
        };
    }

    private static function parseFromString(string $date): self
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d|', $date);
        if ($date === false) {
            throw new \UnexpectedValueException('Invalid date format');
        }

        return new self($date);
    }

    public static function current(Clock $clock = new SystemClock()): self
    {
        return new self($clock->now());
    }

    public function next(): self
    {
        return new self($this->getDateTime()->add(new \DateInterval('P1D')));
    }

    public function previous(): self
    {
        return new self($this->getDateTime()->sub(new \DateInterval('P1D')));
    }

    public function getDayOfWeek(): DayOfWeek
    {
        DayOfWeek::instance($this);
    }

    public function __toString(): string
    {
        return \sprintf("%s-%s", $this->month, \str_pad((string)$this->day, 2, '0', \STR_PAD_LEFT));
    }

    public function getDateTime(): \DateTimeImmutable
    {
        return new \DateTimeImmutable((string)$this);
    }

    public function getYear(): Year
    {
        return $this->month->getLocalYear();
    }

    public function getMonth(): Month
    {
        return $this->month;
    }

    public function getDate(): self
    {
        return $this;
    }

    public function __serialize(): array
    {
        return [
            'date' => (string)$this,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->__construct(\DateTimeImmutable::createFromFormat('Y-m-d|', $data['date']) ?: throw new \RuntimeException(
            'Invalid serialized format for ' . self::class,
        ));
    }
}
