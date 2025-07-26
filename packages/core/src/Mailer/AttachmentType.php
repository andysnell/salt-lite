<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Mailer;

enum AttachmentType
{
    case AttachFromPath;
    case AttachFromContent;
    case EmbedFromPath;
    case EmbedFromContent;
}
