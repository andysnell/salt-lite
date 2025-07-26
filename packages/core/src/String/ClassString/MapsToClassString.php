<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\String\ClassString;

/**
 * @template T of object
 */
interface MapsToClassString
{
    /**
     * @return ClassString<T>
     */
    public function mapsTo(): ClassString;
}
