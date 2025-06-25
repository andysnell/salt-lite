<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\DateTimeAware;
use PhoneBurner\SaltLite\Time\Domain\MonthOfYear;

final class Month implements \Stringable
{
    private const string MONTH_REGEX = '/^([1-9]\d{3})-?(0[1-9]|1[012])$/';

    public readonly MonthOfYear $month;

    public readonly Year $year;

    public function __construct(\DateTimeInterface $date)
    {
        $this->month = MonthOfYear::parse($date);
        $this->year = new Year($date);
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
        if (\preg_match(self::MONTH_REGEX, $date, $matches) !== 1) {
            throw new \UnexpectedValueException('Invalid month format');
        }

        $year = (int)$matches[1];
        $month = (int)$matches[2];

        return new self(new \DateTimeImmutable(sprintf('%04d-%02d-01', $year, $month)));
    }

    public static function current(Clock $clock = new SystemClock()): self
    {
        return new self($clock->now());
    }

    public function __toString(): string
    {
        return sprintf("%s-%s", $this->year, \str_pad((string)$this->month->value, 2, '0', STR_PAD_LEFT));
    }

    public function getLocalYear(): Year
    {
        return $this->year;
    }

    public function getMonth(): self
    {
        return $this;
    }

    public function getMonthOfYear(): MonthOfYear
    {
        return $this->month;
    }

    public function __serialize(): array
    {
        return [
            'date' => (string)$this,
        ];
    }

    public function __unserialize(array $data): void
    {
        $this->__construct(\DateTimeImmutable::createFromFormat('Y-m|', $data['date']) ?: throw new \RuntimeException(
            'Invalid serialized format for ' . self::class
        ));
    }
}
