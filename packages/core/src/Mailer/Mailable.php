<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Mailer;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Domain\Email\EmailAddress;

#[Contract]
interface Mailable
{
    /**
     * @return array<EmailAddress>
     */
    public function getTo(): array;

    public function getSubject(): string;

    public function getBody(): MessageBody|null;
}
