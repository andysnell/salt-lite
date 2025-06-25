<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time;

interface DateTimeAware
{
    public function getDateTime(): \DateTimeImmutable;
}
