<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\TimeZone;

interface TimeZoneCollectionAware
{
    public function getTimeZones(): TimeZoneCollection;
}
