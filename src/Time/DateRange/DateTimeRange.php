<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\DateRange;

class DateTimeRange
{
    public function __construct(
        public \DateTimeImmutable $start,
        public \DateTimeImmutable $end,
    ) {
        if ($start > $end) {
            throw new \InvalidArgumentException('Start date must be before end date.');
        }
    }
}
