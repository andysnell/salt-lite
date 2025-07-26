<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time;

use PhoneBurner\SaltLite\Math\Math;

final readonly class TtlRemaining
{
    public function __construct(public int|float $seconds)
    {
        $seconds >= 0 || throw new \UnexpectedValueException('TTL must be greater than or equal to 0');
    }

    public static function seconds(int|float $seconds): self
    {
        return new self($seconds);
    }

    public static function minutes(int|float $minutes): self
    {
        return new self($minutes * TimeConstant::SECONDS_IN_MINUTE);
    }

    public static function hours(int|float $hours): self
    {
        return new self($hours * TimeConstant::SECONDS_IN_HOUR);
    }

    public static function days(int|float $days): self
    {
        return new self($days * TimeConstant::SECONDS_IN_DAY);
    }

    public static function make(mixed $ttl, \DateTimeImmutable $now = new \DateTimeImmutable()): self
    {
        return match (true) {
            $ttl instanceof self => $ttl,
            $ttl instanceof Ttl => new self($ttl->seconds),
            $ttl instanceof \DateInterval => self::until($now->add($ttl), $now),
            $ttl instanceof \DateTimeInterface => self::until($ttl, $now),
            \is_int($ttl), \is_float($ttl) => new self($ttl),
            \is_numeric($ttl) => new self((float)$ttl),
            default => throw new \UnexpectedValueException('Cannot Convert Value to TTL'),
        };
    }

    public static function min(): self
    {
        static $min = new self(0);
        return $min;
    }

    public static function until(\DateTimeInterface $datetime, \DateTimeInterface $now = new \DateTimeImmutable()): self
    {
        return new self(\max((float)$datetime->format('U.u') - (float)$now->format('U.u'), 0));
    }

    public function inSeconds(): int|float
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
     * @return array{0: int|float}
     */
    public function __serialize(): array
    {
        return [$this->seconds];
    }

    /**
     * @param array{0: int|float} $data
     */
    public function __unserialize(array $data): void
    {
        [$this->seconds] = $data;
    }
}
