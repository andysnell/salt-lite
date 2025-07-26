<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Collections\Map;

use PhoneBurner\SaltLite\Collections\Map\KeyValueStore;
use PhoneBurner\SaltLite\Collections\Map\MapWrapper;
use PhoneBurner\SaltLite\Collections\MapCollection;
use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MapWrapperTest extends TestCase
{
    /** @return MapCollection<mixed> */
    public static function getMockMap(array $data = []): MapCollection
    {
        /**
         * @var MapCollection<mixed> $map
         */
        $map = new readonly class ($data) implements MapCollection {
            /**
             * @use MapWrapper<mixed>
             */
            use MapWrapper;

            private KeyValueStore $map;

            public function __construct(array $data = [])
            {
                $this->map = new KeyValueStore($data);
            }

            /** @return MapCollection<mixed> $map */
            protected function wrapped(): MapCollection
            {
                return $this->map;
            }
        };

        return $map;
    }

    #[Test]
    public function itCanGetAndFindValues(): void
    {
        $map = self::getMockMap(['key1' => 'value1', 'key2' => 'value2']);
        self::assertSame('value1', $map->get('key1'));
        self::assertSame('value2', $map->get('key2'));
        self::assertTrue($map->has('key1'));
        self::assertFalse($map->has('key3'));
        self::assertNull($map->find('invalid_key'));

        $this->expectException(NotFound::class);
        self::assertNull($map->get('invalid_key'));
    }

    #[Test]
    public function itCanSetValues(): void
    {
        $map = self::getMockMap();
        $map->set('key1', 'value1');

        self::assertSame('value1', $map->get('key1'));
        $map->set('key2', 'value2');

        self::assertSame('value2', $map->get('key2'));
    }

    #[Test]
    public function itCanUnsetValues(): void
    {
        $map = self::getMockMap(['key1' => 'value1', 'key2' => 'value2']);
        $map->unset('key1');

        self::assertNull($map->find('key1'));
        self::assertSame('value2', $map->get('key2'));
    }

    #[Test]
    public function itCanClearTheMappedValues(): void
    {
        $map = self::getMockMap(['key1' => 'value1', 'key2' => 'value2']);
        $map->clear();

        self::assertFalse($map->has('key1'));
        self::assertFalse($map->has('key2'));
        self::assertSame([], $map->toArray());
    }

    #[Test]
    public function itCanReturnTheDataAsAnArray(): void
    {
        $array = ['key1' => 'value1', 'key2' => 'value2'];
        $map = self::getMockMap(['key1' => 'value1', 'key2' => 'value2']);

        self::assertSame($array, $map->toArray());
    }

    #[Test]
    public function itCanReplaceTheMappedValues(): void
    {
        $map = self::getMockMap(['key1' => 'value1', 'key2' => 'value2']);
        $map->replace(['key3' => 'value3']);

        self::assertSame(['key3' => 'value3'], $map->toArray());
    }
}
