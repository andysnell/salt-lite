<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Validation;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\SignaturePublicKey;
use PhoneBurner\SaltLite\Cryptography\Natrium;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\DecodedPasetoMessage;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\RegisteredFooterClaim;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\RegisteredPayloadClaim;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paserk;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoPurpose;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoVersion;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;
use PhoneBurner\SaltLite\Time\Clock\Clock;
use PhoneBurner\SaltLite\Time\Ttl;

class ValidationBuilder
{
    public function __construct(
        private readonly Natrium $natrium,
        private readonly Clock $clock,
        public readonly PasetoPurpose|null $purpose = null,
        public readonly array $versions = [PasetoVersion::V4],
        public readonly array $payload_claims = [],
        public readonly array $footer_claims = [],
        public readonly bool $verify_ttl = true,
        public readonly Ttl|null $max_ttl = null,
        public readonly array|null $issuers = null,
        public readonly array|null $subjects = null,
        public readonly array|null $audiences = null,
        public readonly array $callbacks = [],
        public readonly SignaturePublicKey|SharedKey|null $key = null,
        public readonly string $additional_data,
        public readonly array|null $kids = null,
    ) {
    }

    public function withPublicKey(SignaturePublicKey $key, string $additional_data = ''): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            PasetoPurpose::Public,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $key,
            $additional_data,
            $this->kids,
        );
    }

    public function withSharedKey(SharedKey $key, string $additional_data): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            PasetoPurpose::Local,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $key,
            $additional_data,
            $this->kids,
        );
    }

    public function requirePurpose(PasetoPurpose $purpose): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $purpose,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function requireVersion(PasetoVersion ...$versions): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function requirePayloadClaim(RegisteredPayloadClaim|string ...$claims): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            [...$this->payload_claims, ...$claims],
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function requireFooterClaim(RegisteredFooterClaim|string ...$claims): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            [...$this->payload_claims, ...$claims],
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function requireTtlClaims(bool $enable = true): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $enable,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function withIssuer(bool $required = true, string ...$issuers): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $required ? [...$this->payload_claims, RegisteredPayloadClaim::Issuer] : $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function withAudience(bool $required = true, string ...$audiences): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $required ? [...$this->payload_claims, RegisteredPayloadClaim::Audience] : $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function withSubject(bool $required = true, string ...$subjects): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $required ? [...$this->payload_claims, RegisteredPayloadClaim::Subject] : $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function withKid(bool $required = true, Paserk|string ...$kids): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $this->payload_claims,
            $required ? [...$this->footer_claims, RegisteredFooterClaim::KeyId] : $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $kids,
        );
    }

    public function withMaxTokenTtl(Ttl $ttl = new Ttl(300)): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            $this->callbacks,
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    /**
     * @param callable(DecodedPasetoMessage): bool $callback
     */
    public function withCallback(callable $callback): self
    {
        return new self(
            $this->natrium,
            $this->clock,
            $this->purpose,
            $this->versions,
            $this->payload_claims,
            $this->footer_claims,
            $this->verify_ttl,
            $this->max_ttl,
            $this->issuers,
            $this->subjects,
            $this->audiences,
            [...$this->callbacks, $callback],
            $this->key,
            $this->additional_data,
            $this->kids,
        );
    }

    public function validate(): DecodedPasetoMessage|ValidationError
    {
        return new ValidationError();
    }
}
