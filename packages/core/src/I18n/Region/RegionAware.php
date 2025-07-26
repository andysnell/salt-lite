<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\I18n\Region;

interface RegionAware
{
    public function getRegion(): Region;
}
