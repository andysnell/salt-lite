<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Framework\Cache\Config;

use PhoneBurner\SaltLite\Cache\CacheDriver;
use PhoneBurner\SaltLite\Configuration\ConfigStruct;
use PhoneBurner\SaltLite\Configuration\Struct\ConfigStructArrayAccess;
use PhoneBurner\SaltLite\Configuration\Struct\ConfigStructSerialization;
use PhoneBurner\SaltLite\Serialization\Serializer;
use Symfony\Component\Lock\Store\InMemoryStore;
use function PhoneBurner\SaltLite\Framework\env;

final readonly class CacheConfigStruct implements ConfigStruct
{
    use ConfigStructArrayAccess;
    use ConfigStructSerialization;

    public function __construct(
        public array $config = [
            'lock' => [
                'store_driver' => InMemoryStore::class,
            ],
            'drivers' => [
                CacheDriver::Remote->value => [
                    'serializer' => Serializer::Php,
                ],
                CacheDriver::File->value => [

                ],
                CacheDriver::Memory->value => [

                ],
            ],
        ],
    ) {
    }
}
