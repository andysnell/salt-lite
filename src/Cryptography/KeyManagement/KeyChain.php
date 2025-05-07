<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\KeyManagement;

use PhoneBurner\SaltLite\Collections\Map\GenericMapCollection;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignatureKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignaturePublicKey;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paserk;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;
use PhoneBurner\SaltLite\Cryptography\Util;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;

/**
 * Keeps track of keys derived from the app key, for use in encryption and decryption.
 * As a best practice, we want to use a unique key for each type of cryptographic
 * operation, so that a compromise of one key does not compromise all of them.
 *
 * While we need to manage many shared keys, we want to limit the number of asymmetric
 * key pairs used, since the public key is shared with other parties. We derive
 * one X25519 key pair for encryption and one Ed25519 key pair for signatures
 * using a HKDF-Blake2b derivation of the 256-bit app key as the seed.
 *
 * Note: All derived keys can be cleared by calling the clear() method.
 *
 * @extends GenericMapCollection<SharedKey>
 */
final class KeyChain extends GenericMapCollection
{
    private array $signature_keys = [];

    public function __construct(
        public readonly SharedKey $app_key,
        private EncryptionKeyPair|null $encryption_key_pair = null,
        private SignatureKeyPair|null $signature_key_pair = null,
    ) {
        parent::__construct();
    }

    public function shared(string|null $context = null): SharedKey
    {
        return $context === null
            ? $this->app_key
            : $this->remember($context, fn(): SharedKey => KeyDerivation::shared($this->app_key, $context));
    }

    public function encryption(): EncryptionKeyPair
    {
        return $this->encryption_key_pair ??= KeyDerivation::encryption($this->app_key);
    }

    public function signature(): SignatureKeyPair
    {
        return $this->signature_key_pair ??= KeyDerivation::signature($this->app_key);
    }

    public function addSignatureKey(string $context, SignatureKeyPair|SignaturePublicKey $key): void
    {
        // If we already have the keypair in the collection, don't replace it with a public key
        $existing = $this->signature_keys[$context] ?? null;
        if ($existing instanceof SignatureKeyPair && $key instanceof SignaturePublicKey) {
            return;
        }

        // index by context, the public key material, and paserk
        $this->signature_keys[$context] = $key;
        $this->signature_keys[$key->public->bytes()] = $key;
        $this->signature_keys[(string)Paserk::pid($key)] = $key;

        if ($key instanceof SignatureKeyPair) {
            $this->signature_keys[(string)Paserk::sid($key)] = $key;
        }
    }

    public function findSignaturePublicKey(Paserk|BinaryString|string $context): SignaturePublicKey|null
    {
        $context = Util::bytes($context);
        $key = $this->signature_keys[Util::bytes($context)] ?? null;
        if ($key instanceof SignatureKeyPair) {
            return $key->public;
        }

        if ($key instanceof SignaturePublicKey) {
            return $key;
        }

        return null;
    }


    #[\Override]
    public function clear(): void
    {
        $this->encryption_key_pair = null;
        $this->signature_key_pair = null;
        parent::clear();
    }
}
