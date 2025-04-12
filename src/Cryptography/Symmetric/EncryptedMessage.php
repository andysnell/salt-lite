<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Symmetric;

use PhoneBurner\SaltLite\Cryptography\String\Ciphertext;
use PhoneBurner\SaltLite\Cryptography\String\Nonce;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringExportBehavior;
use PhoneBurner\SaltLite\String\Encoding\Encoding;

final readonly class EncryptedMessage implements BinaryString
{
    use BinaryStringExportBehavior;

    public const Encoding DEFAULT_ENCODING = Encoding::Base64Url;

    public function __construct(
        public SymmetricAlgorithm $algorithm,
        #[\SensitiveParameter] public Ciphertext $ciphertext,
        #[\SensitiveParameter] public Nonce $nonce,
    ) {
    }

    /**
     * Returns the nonce prepended to the ciphertext, allowing the nonce to be
     * easily extracted from the message.
     */
    public function bytes(): string
    {
        return $this->nonce->bytes() . $this->ciphertext->bytes();
    }

    /**
     * @return int<0, max>
     */
    public function length(): int
    {
        return $this->nonce->length() + $this->ciphertext->length();
    }

    public function __toString(): string
    {
        return $this->export(self::DEFAULT_ENCODING);
    }

    public function __serialize(): array
    {
        return [
            $this->algorithm->name,
            $this->ciphertext->export(self::DEFAULT_ENCODING),
            $this->nonce->export(self::DEFAULT_ENCODING),
        ];
    }

    /**
     * @param list<string> $data
     */
    public function __unserialize(array $data): void
    {
        $this->__construct(
            SymmetricAlgorithm::{$data[0]},
            Ciphertext::import($data[1], self::DEFAULT_ENCODING),
            Nonce::import($data[2], self::DEFAULT_ENCODING),
        );
    }

    public function jsonSerialize(): string
    {
        return $this->export(self::DEFAULT_ENCODING);
    }
}
