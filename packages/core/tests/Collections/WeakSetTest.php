<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Collections;

use PhoneBurner\SaltLite\Collections\WeakSet;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class WeakSetTest extends TestCase
{
    #[Test]
    public function itShouldAddAndCheckForExistenceOfItems(): void
    {
        $set = new WeakSet();
        $obj = new \stdClass();

        self::assertFalse($set->has($obj));

        $set->add($obj);

        self::assertTrue($set->has($obj));
    }

    #[Test]
    public function itShouldRemoveItems(): void
    {
        $set = new WeakSet();
        $obj = new \stdClass();

        $set->add($obj);
        self::assertTrue($set->has($obj));

        $set->remove($obj);
        self::assertFalse($set->has($obj));
    }

    #[Test]
    public function itShouldBeCountable(): void
    {
        $set = new WeakSet();

        self::assertCount(0, $set);

        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $set->add($obj1);
        $set->add($obj1);
        self::assertCount(1, $set);

        $set->add($obj2);
        self::assertCount(2, $set);

        $set->remove($obj1);
        self::assertCount(1, $set);

        unset($obj1, $obj2);

        self::assertCount(0, $set);
    }

    #[Test]
    public function itShouldClearAllItems(): void
    {
        $set = new WeakSet();
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $set->add($obj1);
        $set->add($obj2);

        self::assertCount(2, $set);

        $set->clear();

        self::assertCount(0, $set);
        self::assertFalse($set->has($obj1));
        self::assertFalse($set->has($obj2));
    }

    #[Test]
    public function itShouldReturnAllItemsAsArray(): void
    {
        $set = new WeakSet();
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $set->add($obj1);
        $set->add($obj2);

        $all = $set->all();

        self::assertCount(2, $all);
        self::assertContains($obj1, $all);
        self::assertContains($obj2, $all);
    }

    #[Test]
    public function itShouldBeIterable(): void
    {
        $set = new WeakSet();
        $obj1 = new \stdClass();
        $obj2 = new \stdClass();

        $set->add($obj1);
        $set->add($obj2);

        $items = [];
        foreach ($set as $item) {
            $items[] = $item;
        }

        self::assertCount(2, $items);
        self::assertContains($obj1, $items);
        self::assertContains($obj2, $items);
    }

    #[Test]
    public function itShouldNotTrackGarbageCollectedObjects(): void
    {
        $set = new WeakSet();

        (static function (WeakSet $set): void {
            $obj = new \stdClass();
            $set->add($obj);
            self::assertCount(1, $set);
            // Object goes out of scope here and should be available for garbage collection
        })($set);

        // Trigger garbage collection
        \gc_collect_cycles();

        self::assertCount(0, $set);
    }

    #[Test]
    public function itShouldAddObjectOnlyOnce(): void
    {
        $set = new WeakSet();
        $obj = new \stdClass();

        $set->add($obj);
        $set->add($obj);

        self::assertCount(1, $set);
    }

    #[Test]
    public function itShouldNotErrorWhenRemovingNonexistentObject(): void
    {
        $set = new WeakSet();
        $obj = new \stdClass();

        // This should not throw an exception
        $set->remove($obj);

        self::assertFalse($set->has($obj));
    }
}
