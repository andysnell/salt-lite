<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Attribute;

use PhoneBurner\SaltLite\Cryptography\Paseto\PaserkType;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoPurpose;

#[\Attribute(\Attribute::TARGET_CLASS_CONSTANT)]
final readonly class PaserkTypeMetadata
{
    public function __construct(
        public PaserkType|null $id,
        public PasetoPurpose $purpose,
        public bool $allowed_in_footer,
        public bool $prefix = false,
    ) {
    }
}
