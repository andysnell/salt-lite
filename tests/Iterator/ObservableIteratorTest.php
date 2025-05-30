<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Iterator;

use PhoneBurner\SaltLite\Iterator\ObservableIterator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ObservableIteratorTest extends TestCase
{
    #[Test]
    public function getIteratorNotifiesObserversOnEachIteration(): void
    {
        $foo = ['foo' => 2343, 'bar' => 23, 'baz' => 32];
        $observer = self::getObserver();
        $sut = new ObservableIterator($foo);
        $sut->attach($observer);

        $counter = 0;
        foreach ($sut as $value) {
            ++$counter;
        }

        self::assertSame(3, $observer->counter);
        self::assertSame([
            ['key' => 'foo', 'value' => 2343],
            ['key' => 'bar', 'value' => 23],
            ['key' => 'baz', 'value' => 32],
        ], $observer->updated);
    }

    #[Test]
    public function detachRemovesObservers(): void
    {
        $foo = ['foo' => 2343, 'bar' => 23, 'baz' => 32, 'qux' => 42];

        $observer_1 = self::getObserver();
        $observer_2 = self::getObserver();

        $sut = new ObservableIterator($foo);
        $sut->attach($observer_1);
        $sut->attach($observer_2);

        $counter = 0;
        foreach ($sut as $value) {
            ++$counter;
            if ($counter === 2) {
                $sut->detach($observer_1);
            }
        }

        self::assertSame(2, $observer_1->counter);
        self::assertSame([
            ['key' => 'foo', 'value' => 2343],
            ['key' => 'bar', 'value' => 23],
        ], $observer_1->updated);

        self::assertSame(4, $observer_2->counter);
        self::assertSame([
            ['key' => 'foo', 'value' => 2343],
            ['key' => 'bar', 'value' => 23],
            ['key' => 'baz', 'value' => 32],
            ['key' => 'qux', 'value' => 42],
        ], $observer_2->updated);
    }

    #[Test]
    public function getIteratorDoesEmptyCase(): void
    {
        $foo = [];
        $observer = self::getObserver();
        $sut = new ObservableIterator($foo);
        $sut->attach($observer);

        $counter = 0;
        foreach ($sut as $value) {
            ++$counter;
        }

        self::assertSame(0, $observer->counter);
        self::assertSame([], $observer->updated);
    }

    /**
     * @return \SplObserver&object{updated: array<array{key: string, value: int}>,counter: int}
     */
    private static function getObserver(): \SplObserver
    {
        return new class implements \SplObserver {
            /**
             * @var array<array{key: string, value: int}>
             */
            public array $updated = [];

            public int $counter = 0;

            public function update(\SplSubject $subject): void
            {
                ++$this->counter;
                \assert($subject instanceof \Iterator);
                $key = $subject->key();
                $current = $subject->current();
                \assert(\is_string($key) && \is_int($current));
                $this->updated[] = ['key' => $key, 'value' => $current];
            }
        };
    }
}
