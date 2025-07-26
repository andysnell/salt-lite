<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Exception;

use PhoneBurner\SaltLite\Serialization\Exception\SerializationFailure;

class CacheMarshallingError extends SerializationFailure implements CacheException
{
}
