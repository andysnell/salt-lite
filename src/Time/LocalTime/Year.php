<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\LocalTime;

use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Clock\SystemClock;
use PhoneBurner\SaltLite\Time\DateTimeAware;

final class Year implements \Stringable
{
    private const string YEAR_REGEX = '/^\d{4}$/';

    public readonly int $year;

    public function __construct(\DateTimeInterface $year)
    {
        $this->year = (int)$year->format('Y');
    }

    public static function current(Clock $clock = new SystemClock()): self
    {
        return new self($clock->now());
    }

    public static function make(
        \DateTimeInterface|DateTimeAware|int|string|null $date = null,
        Clock $clock = new SystemClock(),
    ): self {
        return match (true) {
            $date instanceof \DateTimeInterface => new self($date),
            $date instanceof DateTimeAware => new self($date->getDateTime()),
            $date === null => self::current($clock),
            \is_int($date) => new self(\DateTimeImmutable::createFromTimestamp(0)->setDate($date, 1, 1)),
            default => self::parseFromString((string)$date),
        };
    }

    private static function parseFromString(string $year): self
    {
        if (!\preg_match(self::YEAR_REGEX, $year)) {
            throw new \UnexpectedValueException('Invalid year format');
        }

        return new self(new \DateTimeImmutable(sprintf('%s-01-01', $year)));
    }

    public function __toString(): string
    {
        return (string)$this->year;
    }

    public function getLocalYear(): self
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
        $this->__construct(\DateTimeImmutable::createFromFormat('Y|', $data['date']) ?: throw new \RuntimeException(
            'Invalid serialized format for ' . self::class
        ));
    }
}
