<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\String;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PhoneBurner\SaltLite\Cryptography\Exception\InvalidStringLength;
use PhoneBurner\SaltLite\Cryptography\Util;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\BinaryString\ImportableBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringExportBehavior;

abstract class FixedLengthSensitiveBinaryString implements ImportableBinaryString
{
    use BinaryStringExportBehavior;

    /**
     * Defaults to 256-bit string, but can be overridden in child classes
     *
     * @var non-negative-int
     */
    public const int LENGTH = 32; // 256-bit string

    private string $bytes;

    final public function __construct(#[\SensitiveParameter] BinaryString|string $bytes)
    {
        $bytes = Util::bytes($bytes);
        \strlen($bytes) === static::LENGTH || throw new InvalidStringLength(static::LENGTH);
        $this->bytes = $bytes;
    }

    /**
     * Overwrite the key in memory with null bytes and internally set the value
     * to null when the object is destroyed. This is to prevent the key from leaking
     * into memory dumps or overflows. Doing this requires that the class not be
     * marked as readonly.
     */
    public function __destruct()
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->bytes)) {
            /** @phpstan-ignore assign.propertyType */
            \sodium_memzero($this->bytes);
        }
    }

    /**
     * The return value should always be a string, but there is the technical
     * possibility that it could be null if the object destructor is called
     * manually before the object is cleaned up by the runtime.
     *
     * @return non-empty-string
     */
    public function bytes(): string
    {
        /** @phpstan-ignore nullCoalesce.initializedProperty () */
        return $this->bytes ?: throw CryptoLogicException::unreachable();
    }

    public function length(): int
    {
        return static::LENGTH;
    }

    public function __toString(): string
    {
        return $this->export(static::DEFAULT_ENCODING);
    }

    public function __serialize(): array
    {
        return [$this->export(static::DEFAULT_ENCODING)];
    }

    /**
     * @param list<string> $data
     */
    public function __unserialize(array $data): void
    {
        $this->bytes = ConstantTime::decode(static::DEFAULT_ENCODING, $data[0]);
    }

    public function jsonSerialize(): string
    {
        return $this->export(static::DEFAULT_ENCODING);
    }
}
