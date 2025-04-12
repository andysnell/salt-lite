<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Iterator;

use IteratorIterator;
use PhoneBurner\SaltLite\Iterator\Iter;

/**
 * @template TKey
 * @template TValue
 * @extends IteratorIterator<TKey, TValue, \Iterator>
 */
class ObservableIterator extends IteratorIterator implements \SplSubject
{
    /**
     * @var \SplObjectStorage<\SplObserver, null>
     */
    private readonly \SplObjectStorage $observers;

    /**
     * @param iterable<TKey, TValue> $iterable
     */
    public function __construct(iterable $iterable)
    {
        parent::__construct(Iter::cast($iterable));
        $this->observers = new \SplObjectStorage();
    }

    #[\Override]
    public function valid(): bool
    {
        if (parent::valid()) {
            $this->notify();
            return true;
        }
        return false;
    }

    #[\Override]
    public function attach(\SplObserver $observer): void
    {
        $this->observers->attach($observer);
    }

    #[\Override]
    public function detach(\SplObserver $observer): void
    {
        $this->observers->detach($observer);
    }

    #[\Override]
    public function notify(): void
    {
        foreach ($this->observers as $observer) {
            $observer->update($this);
        }
    }
}
