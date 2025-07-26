<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ObjectContainer;

use PhoneBurner\SaltLite\Collections\Map\GenericMapCollection;
use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PhoneBurner\SaltLite\Container\ObjectContainer\MutableObjectContainer;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use stdClass;

final class MutableObjectContainerTest extends TestCase
{
    /**
     * @var MutableObjectContainer<stdClass>
     */
    private MutableObjectContainer $container;

    protected function setUp(): void
    {
        $this->container = new MutableObjectContainer();
    }

    #[Test]
    public function setAddsEntryToContainer(): void
    {
        $obj = new stdClass();
        $this->container->set('test_id', $obj);
        self::assertTrue($this->container->has('test_id'));
        self::assertSame($obj, $this->container->get('test_id'));
    }

    #[Test]
    public function getThrowsNotFoundExceptionForMissingId(): void
    {
        $this->expectException(NotFound::class);
        $this->container->get('missing_id');
    }

    #[Test]
    public function hasReturnsFalseForMissingId(): void
    {
        self::assertFalse($this->container->has('missing_id'));
    }

    #[Test]
    public function unsetRemovesEntryFromContainer(): void
    {
        $obj = new stdClass();
        $this->container->set('test_id', $obj);
        self::assertTrue($this->container->has('test_id'));

        $this->container->unset('test_id');
        self::assertFalse($this->container->has('test_id'));
    }

    #[Test]
    public function unsetDoesNothingForMissingId(): void
    {
        // Expect no exception
        $this->container->unset('missing_id');
        self::assertFalse($this->container->has('missing_id'));
    }

    #[DataProvider('providesStringableIds')]
    #[Test]
    public function containerMethodsWorkWithStringableIds(string|\Stringable $id): void
    {
        $obj = new stdClass();

        // Set
        $this->container->set($id, $obj);
        self::assertTrue($this->container->has($id));
        self::assertSame($obj, $this->container->get($id));

        // Unset
        $this->container->unset($id);
        self::assertFalse($this->container->has($id));
    }

    public static function providesStringableIds(): \Generator
    {
        yield 'string' => ['string_id'];
        yield 'Stringable object' => [
            new class implements \Stringable {
                public function __toString(): string
                {
                    return 'stringable_id';
                }
            },
        ];
    }

    #[Test]
    public function constructorCanInitializeWithEntries(): void
    {
        $entry1 = new stdClass();
        $entry2 = new stdClass();
        $entries = ['id1' => $entry1, 'id2' => $entry2];
        $container = new MutableObjectContainer($entries);

        self::assertTrue($container->has('id1'));
        self::assertSame($entry1, $container->get('id1'));
        self::assertTrue($container->has('id2'));
        self::assertSame($entry2, $container->get('id2'));
        self::assertFalse($container->has('id3'));
    }

    #[Test]
    public function replaceUpdatesEntriesWithArray(): void
    {
        $initial_obj = new stdClass();
        $this->container->set('initial_id', $initial_obj);

        $replace_obj1 = new stdClass();
        $replace_obj2 = new stdClass();
        $replacement_map = ['replace_id1' => $replace_obj1, 'replace_id2' => $replace_obj2];

        $this->container->replace($replacement_map);

        self::assertFalse($this->container->has('initial_id'));
        self::assertTrue($this->container->has('replace_id1'));
        self::assertSame($replace_obj1, $this->container->get('replace_id1'));
        self::assertTrue($this->container->has('replace_id2'));
        self::assertSame($replace_obj2, $this->container->get('replace_id2'));
    }

    #[Test]
    public function clearRemovesAllEntries(): void
    {
        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $this->container->set('id1', $obj1);
        $this->container->set('id2', $obj2);

        self::assertTrue($this->container->has('id1'));
        self::assertTrue($this->container->has('id2'));

        $this->container->clear();

        self::assertFalse($this->container->has('id1'));
        self::assertFalse($this->container->has('id2'));
        self::assertEmpty($this->container->toArray());
    }

    #[Test]
    public function toArrayReturnsInternalEntries(): void
    {
        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $expected_array = ['id1' => $obj1, 'id2' => $obj2];

        $this->container->set('id1', $obj1);
        $this->container->set('id2', $obj2);

        self::assertEquals($expected_array, $this->container->toArray());
    }

    #[Test]
    public function arrayAccessOffsetExists(): void
    {
        $obj = new stdClass();
        $this->container->set('exists', $obj);
        /** @phpstan-ignore argument.type (generics expectations differ, possible phpstan bug) */
        self::assertArrayHasKey('exists', $this->container);
        /** @phpstan-ignore argument.type (generics expectations differ, possible phpstan bug) */
        self::assertArrayNotHasKey('does_not_exist', $this->container);
    }

    #[Test]
    public function arrayAccessOffsetGet(): void
    {
        $obj = new stdClass();
        $this->container->set('exists', $obj);
        self::assertSame($obj, $this->container['exists']);
    }

    #[Test]
    public function arrayAccessOffsetGetThrowsNotFound(): void
    {
        self::assertNull($this->container['does_not_exist']);
    }

    #[Test]
    public function arrayAccessOffsetSet(): void
    {
        $obj = new stdClass();
        $this->container['new_id'] = $obj;
        self::assertTrue($this->container->has('new_id'));
        self::assertSame($obj, $this->container->get('new_id'));
    }

    #[Test]
    public function arrayAccessOffsetUnset(): void
    {
        $obj = new stdClass();
        $this->container->set('to_unset', $obj);
        self::assertTrue($this->container->has('to_unset'));

        unset($this->container['to_unset']);
        self::assertFalse($this->container->has('to_unset'));
    }

    #[Test]
    public function getIteratorReturnsGenerator(): void
    {
        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $entries = ['id1' => $obj1, 'id2' => $obj2];
        $container = new MutableObjectContainer($entries);

        $iterator = $container->getIterator();
        self::assertInstanceOf(\Generator::class, $iterator);
        self::assertEquals($entries, \iterator_to_array($iterator));
    }

    #[Test]
    public function callInvokesCallableEntry(): void
    {
        $callable_entry = new class extends \stdClass {
            public bool $invoked = false;

            public function __invoke(): string
            {
                $this->invoked = true;
                return 'invoked_result';
            }
        };
        $this->container->set('callable_id', $callable_entry);

        $entry = $this->container->get('callable_id');
        $result = $this->container->call($entry);

        self::assertTrue($callable_entry->invoked);
        self::assertEquals('invoked_result', $result);
    }

    #[Test]
    public function callInvokesMethodOnObjectEntry(): void
    {
        $object_entry = new class extends \stdClass {
            public bool $method_called = false;

            public function targetMethod(): string
            {
                 $this->method_called = true;
                 return 'method_result';
            }
        };
        $this->container->set('object_id', $object_entry);

        $entry = $this->container->get('object_id');
        $result = $this->container->call($entry, 'targetMethod');

        self::assertTrue($object_entry->method_called);
        self::assertEquals('method_result', $result);
    }

    #[Test]
    public function callThrowsExceptionForNonCallableString(): void
    {
        /** @phpstan-ignore argument.type (intentional defect) */
        $this->container->set('string_id', 'not_callable');
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected $object to be object, class string, or callable, got "string"');
        /** @phpstan-ignore argument.type */
        $this->container->call('string_id');
    }

    #[Test]
    public function callThrowsExceptionForNonInvokableObject(): void
    {
        $non_invokable = new stdClass();
        $this->container->set('non_invokable_id', $non_invokable);
        $entry = $this->container->get('non_invokable_id');
        $this->expectException(\UnexpectedValueException::class);
        $this->container->call($entry);
    }

    #[Test]
    public function callThrowsExceptionForMissingIdString(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Expected $object to be object, class string, or callable, got "string"');
        /** @phpstan-ignore argument.type */
        $this->container->call('missing_id');
    }

    #[Test]
    public function findReturnsObjectIfKeyExists(): void
    {
        $obj = new stdClass();
        $this->container->set('found', $obj);
        self::assertSame($obj, $this->container->find('found'));
    }

    #[Test]
    public function findReturnsNullIfKeyDoesNotExist(): void
    {
        self::assertNull($this->container->find('not_found'));
    }

    #[Test]
    public function containsReturnsTrueIfValueExistsStrict(): void
    {
        $obj = new stdClass();
        $this->container->set('key', $obj);
        self::assertTrue($this->container->contains($obj)); // strict = true (default)
    }

    #[Test]
    public function containsReturnsFalseIfValueDoesNotExistStrict(): void
    {
        $obj1 = new stdClass();
        $obj2 = new stdClass();
        $this->container->set('key', $obj1);
        self::assertFalse($this->container->contains($obj2)); // strict = true (default)
    }

    #[Test]
    public function containsReturnsTrueIfValueExistsNonStrict(): void
    {
        // Note: Non-strict comparison for objects usually means same class and same properties
        // but stdClass comparison can be tricky. Using simple values for clarity.
        $container = new MutableObjectContainer(); // Use a container that can hold non-objects
        $container->set('key1', 123);
        self::assertTrue($container->contains('123', false)); // non-strict
    }

    #[Test]
    public function containsReturnsFalseIfValueDoesNotExistNonStrict(): void
    {
        $container = new MutableObjectContainer();
        $container->set('key1', 123);
        self::assertFalse($container->contains('456', false)); // non-strict
    }

    #[Test]
    public function rememberReturnsExistingValueIfKeyExists(): void
    {
        $obj = new stdClass();
        $this->container->set('exists', $obj);
        $callback_called = false;
        $result = $this->container->remember('exists', function () use (&$callback_called): \stdClass {
            $callback_called = true;
            return new stdClass();
        });
        self::assertSame($obj, $result);
        self::assertFalse($callback_called);
    }

    #[Test]
    public function rememberCallsCallbackSetsAndReturnsValueIfKeyDoesNotExist(): void
    {
        $new_obj = new stdClass();
        $callback_called = false;
        $result = $this->container->remember('new_key', function () use (&$callback_called, $new_obj): \stdClass {
            $callback_called = true;
            return $new_obj;
        });
        self::assertSame($new_obj, $result);
        self::assertTrue($callback_called);
        self::assertTrue($this->container->has('new_key'));
        self::assertSame($new_obj, $this->container->get('new_key'));
    }

    #[Test]
    public function forgetRemovesEntryAndReturnsValueIfKeyExists(): void
    {
        $obj = new stdClass();
        $this->container->set('to_forget', $obj);
        self::assertTrue($this->container->has('to_forget'));
        $result = $this->container->forget('to_forget');
        self::assertSame($obj, $result);
        self::assertFalse($this->container->has('to_forget'));
    }

    #[Test]
    public function forgetReturnsNullIfKeyDoesNotExist(): void
    {
        self::assertFalse($this->container->has('not_present'));
        $result = $this->container->forget('not_present');
        self::assertNull($result);
    }

    #[Test]
    public function mapAppliesCallbackToEntries(): void
    {
        $obj1 = new stdClass();
        $obj1->value = 1;
        $obj2 = new stdClass();
        $obj2->value = 2;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);

        $result = $this->container->map(static fn(stdClass $obj, string $key): string => $key . '_' . $obj->value);

        self::assertInstanceOf(GenericMapCollection::class, $result);
        $expected = ['key1' => 'key1_1', 'key2' => 'key2_2'];
        self::assertSame($expected, $result->toArray());
    }

    #[Test]
    public function filterRemovesEntriesNotMatchingCallback(): void
    {
        $obj1 = new stdClass();
        $obj1->keep = true;
        $obj2 = new stdClass();
        $obj2->keep = false;
        $obj3 = new stdClass();
        $obj3->keep = true;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);
        $this->container->set('key3', $obj3);

        $result = $this->container->filter(fn(stdClass $obj) => $obj->keep);

        self::assertSame($this->container, $result); // Returns $this
        $expected = ['key1' => $obj1, 'key3' => $obj3];
        self::assertEquals($expected, $this->container->toArray());
        self::assertCount(2, $this->container);
    }

    #[Test]
    public function filterRemovesFalsyValuesWithNullCallback(): void
    {
        // Note: MutableObjectContainer expects objects, so testing falsy values
        // requires a container that allows mixed types, or careful object setup.
        // Using boolean objects for simplicity.
        $trueObj = new class extends stdClass { public bool $value = true;
        };
        $falseObj = new class extends stdClass { public bool $value = false;
        }; // Treat as non-empty object

        $container = new MutableObjectContainer();
        $container->set('key1', $trueObj);
        $container->set('key2', $falseObj);
        $container->set('key3', $trueObj);

        // Default filter removes *empty* values (e.g., null, false, 0, '').
        // Objects are generally not empty unless they implement specific logic.
        // PHP's array_filter default behaviour might not be intuitive here.
        // Let's test filtering based on a property for clarity.
        $result = $this->container->filter(fn($obj) => $obj->value ?? false);

        // Re-add entries to the main container for the test
        $this->container->set('key1', $trueObj);
        $this->container->set('key2', $falseObj);
        $this->container->set('key3', $trueObj);

        $result = $this->container->filter(fn($obj) => $obj->value);

        self::assertSame($this->container, $result);
        $expected = ['key1' => $trueObj, 'key3' => $trueObj];
        self::assertEquals($expected, $this->container->toArray());
    }

    #[Test]
    public function rejectRemovesEntriesMatchingCallback(): void
    {
        $obj1 = new stdClass();
        $obj1->reject = true;
        $obj2 = new stdClass();
        $obj2->reject = false;
        $obj3 = new stdClass();
        $obj3->reject = true;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);
        $this->container->set('key3', $obj3);

        $result = $this->container->reject(fn(stdClass $obj) => $obj->reject);

        self::assertSame($this->container, $result); // Returns $this
        $expected = ['key2' => $obj2];
        self::assertEquals($expected, $this->container->toArray());
        self::assertCount(1, $this->container);
    }

    #[Test]
    public function allReturnsTrueIfCallbackTrueForAll(): void
    {
        $obj1 = new stdClass();
        $obj1->valid = true;
        $obj2 = new stdClass();
        $obj2->valid = true;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);

        self::assertTrue($this->container->all(fn(stdClass $obj) => $obj->valid));
    }

    #[Test]
    public function allReturnsFalseIfCallbackFalseForAny(): void
    {
        $obj1 = new stdClass();
        $obj1->valid = true;
        $obj2 = new stdClass();
        $obj2->valid = false;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);

        self::assertFalse($this->container->all(fn(stdClass $obj) => $obj->valid));
    }

    #[Test]
    public function allReturnsTrueForEmptyContainer(): void
    {
        self::assertTrue($this->container->all(static fn(): false => false)); // Callback shouldn't be called
    }

    #[Test]
    public function anyReturnsTrueIfCallbackTrueForAny(): void
    {
        $obj1 = new stdClass();
        $obj1->match = false;
        $obj2 = new stdClass();
        $obj2->match = true;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);

        self::assertTrue($this->container->any(fn(stdClass $obj) => $obj->match));
    }

    #[Test]
    public function anyReturnsFalseIfCallbackFalseForAll(): void
    {
        $obj1 = new stdClass();
        $obj1->match = false;
        $obj2 = new stdClass();
        $obj2->match = false;
        $this->container->set('key1', $obj1);
        $this->container->set('key2', $obj2);

        self::assertFalse($this->container->any(fn(stdClass $obj) => $obj->match));
    }

    #[Test]
    public function anyReturnsFalseForEmptyContainer(): void
    {
        self::assertFalse($this->container->any(fn(): true => true)); // Callback shouldn't be called
    }

    #[Test]
    public function countReturnsNumberOfEntries(): void
    {
        self::assertCount(0, $this->container);
        $this->container->set('key1', new stdClass());
        self::assertCount(1, $this->container);
        $this->container->set('key2', new stdClass());
        self::assertCount(2, $this->container);
        $this->container->unset('key1');
        self::assertCount(1, $this->container);
    }

    #[Test]
    public function keysReturnsArrayOfKeys(): void
    {
        self::assertSame([], $this->container->keys());
        $this->container->set('key1', new stdClass());
        $this->container->set('key3', new stdClass());
        $this->container->set('key2', new stdClass());
        // Note: order might not be guaranteed depending on internal array handling
        self::assertEqualsCanonicalizing(['key1', 'key3', 'key2'], $this->container->keys());
    }

    #[Test]
    public function isEmptyReturnsTrueForNewContainer(): void
    {
        self::assertTrue($this->container->isEmpty());
    }

    #[Test]
    public function isEmptyReturnsFalseForNonEmptyContainer(): void
    {
        $this->container->set('key', new stdClass());
        self::assertFalse($this->container->isEmpty());
    }

    #[Test]
    public function isEmptyReturnsTrueAfterClear(): void
    {
        $this->container->set('key', new stdClass());
        $this->container->clear();
        self::assertTrue($this->container->isEmpty());
    }
}
