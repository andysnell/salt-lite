<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Serialization\PhpSerializable;
use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneCollection;
use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneCollectionAware;
use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneFactory;

/**
 * @implements PhpSerializable<array{npa: int<200,999>}>
 */
#[Contract]
final class AreaCode implements
    \Stringable,
    AreaCodeAware,
    TimeZoneCollectionAware,
    PhpSerializable
{
    /**
     * Numbering Plan Area (NPA) Code for this Area Code.
     *
     * @var int<200,999>
     */
    public int $npa;

    /**
     * @var int-mask-of<AreaCodeStatus::*>
     */
    // phpcs:disable
    public int $status {
        get {
            static $cache = [];
            return $cache[$this->npa] ??= AreaCodeStatus::mask($this->metadata);
        }
    }
    // phpcs:enable

    // phpcs:disable
    public AreaCodePurpose $purpose {
        get {
            static $cache = [];
            return $cache[$this->npa] ??= AreaCodePurpose::tryFrom($this->metadata >> 8 & 0xFF)
                ?? AreaCodePurpose::GeneralPurpose;
        }
    }
    // phpcs:enable

    // phpcs:disable
    public TimeZoneCollection $time_zones {
        get {
            static $cache = [];
            return $cache[$this->npa] ??= $this->metadata & AreaCodeStatus::ASSIGNABLE
                ? TimeZoneFactory::collect(...AreaCodeData::TIME_ZONE_MAP[$this->metadata >> 16 & 0xFF])
                : TimeZoneFactory::collect();
        }
    }
    // phpcs:enable

    // phpcs:disable
    public AreaCodeLocation $location {
        get {
            static $cache = [];
            return $cache[$this->npa] ??= AreaCodeLocation::make(...AreaCodeData::LOCATION_MAP[$this->metadata >> 24 & 0xFF]);
        }
    }
    // phpcs:enable

    // phpcs:disable
    protected int $metadata {
        get => AreaCodeData::METADATA[$this->npa];
    }
    // phpcs:enable phpcs:ignore

    public function __construct(int $npa)
    {
        if ($npa < 200 || $npa > 999) {
            throw new \UnexpectedValueException('Invalid Area Code NPA Value');
        }

        $this->npa = $npa;
    }

    public static function make(AreaCodeAware|int|string $area_code): self
    {
        static $cache = [];
        return $area_code instanceof AreaCodeAware
            ? $area_code->getAreaCode()
            : $cache[(int)$area_code] ??= new self((int)$area_code);
    }

    public static function tryFrom(mixed $area_code): self|null
    {
        try {
            return match (true) {
                $area_code instanceof self => $area_code,
                \is_int($area_code), \is_string($area_code) => self::make($area_code),
                $area_code instanceof AreaCodeAware => $area_code->getAreaCode(),
                default => null,
            };
        } catch (\Throwable) {
            return null;
        }
    }

    public static function all(): AreaCodeCollection
    {
        return new AreaCodeCollection(...\array_map([self::class, 'make'], \range(200, 999)));
    }

    public static function active(): AreaCodeCollection
    {
        return self::all()->filter(static fn(self $area_code): bool => $area_code->isActive());
    }

    #[\Override]
    public function getAreaCode(): self
    {
        return $this;
    }

    #[\Override]
    public function getTimeZones(): TimeZoneCollection
    {
        return $this->time_zones;
    }

    /**
     * We consider area codes that are in the process of activating, e.g. a
     * new overlay that may start issuing numbers at some point in time after
     * it is scheduled, to also be "active" to account for delays in updates
     * to libphonenumber and the upstream NANP database not tracking an actual
     * "go live" date for each area code.
     */
    public function isActive(): bool
    {
        return $this->status & AreaCodeStatus::ACTIVE
            || $this->status === (AreaCodeStatus::ASSIGNABLE | AreaCodeStatus::ASSIGNED | AreaCodeStatus::SCHEDULED);
    }

    #[\Override]
    public function __toString(): string
    {
        return (string)$this->npa;
    }

    #[\Override]
    public function __serialize(): array
    {
        return ['npa' => $this->npa];
    }

    #[\Override]
    public function __unserialize(array $data): void
    {
        $this->__construct($data['npa']);
    }
}
