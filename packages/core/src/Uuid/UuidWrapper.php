<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Uuid;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\UuidInterface;

/**
 * @phpstan-require-implements UuidInterface
 */
trait UuidWrapper
{
    abstract protected function uuid(): UuidInterface;

    /**
     * @return array{uuid:non-empty-string} $data
     */
    abstract public function __serialize(): array;

    /**
     * @param array{uuid:non-empty-string} $data
     */
    abstract public function __unserialize(array $data): void;

    public function compareTo(UuidInterface $other): int
    {
        return $this->uuid()->compareTo($other);
    }

    public function equals(object|null $other): bool
    {
        return $this->uuid()->equals($other);
    }

    public function getBytes(): string
    {
        return $this->uuid()->getBytes();
    }

    public function getFields(): FieldsInterface
    {
        return $this->uuid()->getFields();
    }

    public function getHex(): Hexadecimal
    {
        return $this->uuid()->getHex();
    }

    public function getInteger(): IntegerObject
    {
        return $this->uuid()->getInteger();
    }

    public function getUrn(): string
    {
        return $this->uuid()->getUrn();
    }

    public function toString(): string
    {
        return $this->uuid()->toString();
    }

    public function __toString(): string
    {
        return (string)$this->uuid();
    }

    /**
     * The UuidInterface extends the JsonSerializable interface, but does not
     * narrow the type to string from mixed. Since we want to be strict about
     * the type we return, we need to manually call the toString method (which
     * is what the implementations of the interface are doing anyway).
     */
    public function jsonSerialize(): string
    {
        return $this->uuid()->toString();
    }

    #[\Deprecated]
    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->uuid()->getNumberConverter();
    }

    #[\Deprecated]
    public function getFieldsHex(): array
    {
        return $this->uuid()->getFieldsHex();
    }

    #[\Deprecated]
    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->uuid()->getClockSeqHiAndReservedHex();
    }

    #[\Deprecated]
    public function getClockSeqLowHex(): string
    {
        return $this->uuid()->getClockSeqLowHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getClockSequenceHex(): string
    {
        return $this->uuid()->getClockSequenceHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getDateTime(): DateTimeInterface
    {
        return $this->uuid()->getDateTime();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getLeastSignificantBitsHex(): string
    {
        return $this->uuid()->getLeastSignificantBitsHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getMostSignificantBitsHex(): string
    {
        return $this->uuid()->getMostSignificantBitsHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getNodeHex(): string
    {
        return $this->uuid()->getNodeHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getTimeHiAndVersionHex(): string
    {
        return $this->uuid()->getTimeHiAndVersionHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getTimeLowHex(): string
    {
        return $this->uuid()->getTimeLowHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getTimeMidHex(): string
    {
        return $this->uuid()->getTimeMidHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getTimestampHex(): string
    {
        return $this->uuid()->getTimestampHex();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getVariant(): int|null
    {
        return $this->uuid()->getVariant();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function getVersion(): int|null
    {
        return $this->uuid()->getVersion();
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function serialize(): never
    {
        throw new \LogicException('Serializes with __serialize Magic Method');
    }

    /**
     * @codeCoverageIgnore
     */
    #[\Deprecated]
    public function unserialize($data): never
    {
        throw new \LogicException('Deserializes with __unserialize Magic Method');
    }
}
