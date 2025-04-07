<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Claims;

use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\DecodedFooterClaims;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\DecodedPayloadClaims;
use PhoneBurner\SaltLite\Cryptography\Paseto\Claims\PasetoMessage;

final readonly class DecodedPasetoMessage
{
    public DecodedPayloadClaims $payload;
    public DecodedFooterClaims $footer;

    public function __construct(string $json_encoded_payload, string $json_encoded_footer = '')
    {
        $this->payload = DecodedPayloadClaims::make($json_encoded_payload);
        $this->footer = DecodedFooterClaims::make($json_encoded_footer);
    }

    public static function make(PasetoMessage $message): self
    {
        return new self($message->payload, $message->footer);
    }
}
