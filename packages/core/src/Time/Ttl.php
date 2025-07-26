<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time;

use PhoneBurner\SaltLite\Math\Math;

final readonly class Ttl
{
    public const int DEFAULT_SECONDS = 5 * TimeConstant::SECONDS_IN_MINUTE;

    public int $seconds;

    public function __construct(int|float $seconds = self::DEFAULT_SECONDS)
    {
        $seconds >= 0 || throw new \UnexpectedValueException('TTL must be greater than or equal to 0');
        $seconds > \PHP_INT_MAX && throw new \UnexpectedValueException('TTL must be less than or equal to ' . \PHP_INT_MAX);
        $this->seconds = (int)$seconds;
    }

    /**
     * This named constructor is designed to be backwards compatible with the
     * various ways a time-to-live value can be referenced from both of the PSRs
     * concerned with caching.
     */
    public static function make(mixed $ttl, \DateTimeImmutable $now = new \DateTimeImmutable()): self
    {
        return match (true) {
            $ttl instanceof self => $ttl,
            $ttl instanceof \DateInterval => self::until($now->add($ttl), $now),
            $ttl instanceof \DateTimeInterface => self::until($ttl, $now),
            $ttl === null => self::max(),
            \is_int($ttl) => new self($ttl),
            \is_numeric($ttl) => new self((int)$ttl),
            default => throw new \UnexpectedValueException('Cannot Convert Value to TTL'),
        };
    }

    public static function seconds(int|float $seconds = self::DEFAULT_SECONDS): self
    {
        return new self($seconds);
    }

    public static function minutes(int|float $minutes = 5): self
    {
        return new self($minutes * TimeConstant::SECONDS_IN_MINUTE);
    }

    public static function hours(int $hours = 1): self
    {
        return new self($hours * TimeConstant::SECONDS_IN_HOUR);
    }

    public static function days(int $days = 1): self
    {
        return new self($days * TimeConstant::SECONDS_IN_DAY);
    }

    public static function until(\DateTimeInterface $datetime, \DateTimeInterface $now = new \DateTimeImmutable()): self
    {
        return new self($datetime->getTimestamp() - $now->getTimestamp());
    }

    public static function max(): self
    {
        static $max = new self(\PHP_INT_MAX);
        return $max;
    }

    public static function min(): self
    {
        static $min = new self(0);
        return $min;
    }

    public function inSeconds(): int
    {
        return $this->seconds;
    }

    public function inMinutes(): int
    {
        return Math::floor($this->seconds / TimeConstant::SECONDS_IN_MINUTE);
    }

    public function inHours(): int
    {
        return Math::floor($this->seconds / TimeConstant::SECONDS_IN_HOUR);
    }

    public function toDateInterval(): \DateInterval
    {
        return new \DateInterval('PT' . $this->seconds . 'S');
    }

    /**
     * @return array{0: int}
     */
    public function __serialize(): array
    {
        return [$this->seconds];
    }

    /**
     * @param array{0: int} $data
     */
    public function __unserialize(array $data): void
    {
        [$this->seconds] = $data;
    }
}
