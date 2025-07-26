<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Random;

use PhoneBurner\SaltLite\Random\Randomizer;
use PhoneBurner\SaltLite\Random\WeightedItem;
use PhoneBurner\SaltLite\Tests\Fixtures\EmptyEnum;
use PhoneBurner\SaltLite\Tests\Fixtures\StoplightState;
use PhoneBurner\SaltLite\Tests\Fixtures\TestEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;
use Random\IntervalBoundary;

final class RandomizerTest extends TestCase
{
    private Randomizer $randomizer;

    protected function setUp(): void
    {
        parent::setUp();
        // Instantiate Random directly, relying on the default Secure engine
        $this->randomizer = new Randomizer();
    }

    #[Test]
    public function bytesReturnsCorrectLength(): void
    {
        $length = 16;
        $bytes = $this->randomizer->bytes($length);
        self::assertSame($length, \strlen($bytes));
    }

    #[Test]
    public function bytesThrowsExceptionForZeroBytes(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('bytes must be greater than 0');
        $this->randomizer->bytes(0);
    }

    #[Test]
    public function bytesThrowsExceptionForNegativeBytes(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('bytes must be greater than 0');
        $this->randomizer->bytes(-1);
    }

    #[Test]
    public function charsReturnsCorrectLength(): void
    {
        $length = 20;
        $chars_set = 'abc';
        $result = $this->randomizer->chars($length, $chars_set);
        self::assertSame($length, \strlen($result));
    }

    #[Test]
    public function charsUsesSpecifiedCharset(): void
    {
        $length = 30;
        $chars_set = '01'; // Only 0s and 1s
        $result = $this->randomizer->chars($length, $chars_set);
        self::assertMatchesRegularExpression('/^[01]+$/', $result, 'String should only contain characters from the specified set');
        self::assertSame($length, \strlen($result));
    }

    #[Test]
    public function charsUsesDefaultCharsetWhenNotProvided(): void
    {
        $length = 30;
        $result = $this->randomizer->chars($length); // Use default ALPHANUMERIC
        // Check if all characters are alphanumeric
        self::assertMatchesRegularExpression('/^[a-zA-Z0-9]+$/', $result, 'String should only contain default alphanumeric characters');
        self::assertSame($length, \strlen($result));
    }

    #[Test]
    public function charsThrowsExceptionForZeroLength(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('length must be greater than 0');
        $this->randomizer->chars(0);
    }

    #[Test]
    public function charsThrowsExceptionForEmptyCharacterSet(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('chars string must not be empty');
        $this->randomizer->chars(10, ''); // Empty character set
    }

    #[Test]
    public function charsThrowsExceptionForNegativeLength(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('length must be greater than 0');
        $this->randomizer->chars(-1);
    }

    #[Test]
    public function intReturnsValueWithinRange(): void
    {
        $min = 10;
        $max = 20;
        $result = $this->randomizer->int($min, $max);
        self::assertGreaterThanOrEqual($min, $result);
        self::assertLessThanOrEqual($max, $result);
    }

    #[Test]
    public function intReturnsValueWithinDefaultRange(): void
    {
        $result = $this->randomizer->int();
        self::assertGreaterThanOrEqual(\PHP_INT_MIN, $result);
        self::assertLessThanOrEqual(\PHP_INT_MAX, $result);
    }

    #[Test]
    public function intReturnsSameValueWhenMinEqualsMax(): void
    {
        self::assertSame(42, $this->randomizer->int(42, 42));
    }

    #[Test]
    public function intThrowsExceptionWhenMinGreaterThanMax(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('min must be less than or equal to max'); // Corrected expected message
        $this->randomizer->int(100, 10);
    }

    #[Test]
    #[TestWith([0.1, 0.9, IntervalBoundary::ClosedOpen])] // )]
    #[TestWith([-1.0, 1.0, IntervalBoundary::ClosedClosed])] // []
    #[TestWith([5.0, 6.0, IntervalBoundary::OpenOpen])] // (())
    #[TestWith([10.0, 10.1, IntervalBoundary::OpenClosed])] // (]
    public function floatReturnsValueWithinRangeAndBoundary(float $min, float $max, IntervalBoundary $boundary): void
    {
        $result = $this->randomizer->float($min, $max, $boundary);

        match ($boundary) {
            IntervalBoundary::ClosedOpen => self::assertGreaterThanOrEqual($min, $result), // [
            IntervalBoundary::OpenOpen, IntervalBoundary::OpenClosed => self::assertGreaterThan($min, $result), // (
            default => self::assertGreaterThanOrEqual($min, $result),
        };

        match ($boundary) {
            IntervalBoundary::OpenOpen => self::assertLessThan($max, $result), // )
            IntervalBoundary::ClosedOpen, IntervalBoundary::OpenClosed => self::assertLessThanOrEqual($max, $result), // ]
            default => self::assertLessThanOrEqual($max, $result),
        };

        // Specific checks for open/closed boundaries if min=max (though Randomizer might handle this)
        if ($min === $max) {
            if ($boundary === IntervalBoundary::OpenOpen || $boundary === IntervalBoundary::OpenClosed || $boundary === IntervalBoundary::ClosedOpen) {
                // It's impossible to satisfy open boundaries if min==max, expect error? PHP Randomizer seems to allow it though.
                 self::fail('Test case with open boundary and min==max might be invalid or requires specific expectation');
            } else { // ClosedClosed
                self::assertSame($min, $result);
            }
        }
    }

    #[Test]
    public function floatUsesDefaultArgsWhenNotProvided(): void
    {
        $result = $this->randomizer->float(); // Default 0.0, 1.0, ClosedOpen [0, 1)
        self::assertGreaterThanOrEqual(0.0, $result);
        self::assertLessThan(1.0, $result); // Strictly less than 1.0 due to ClosedOpen
    }

    #[Test]
    public function floatThrowsExceptionWhenMinGreaterThanMax(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('min must be less than or equal to max');
        $this->randomizer->float(1.0, 0.5);
    }

    #[Test]
    public function keysReturnsCorrectNumberOfKeys(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        $num = 3;
        $keys = $this->randomizer->keys($array, $num);
        self::assertCount($num, $keys);
    }

    #[Test]
    #[TestWith([2, ['ab', 'bc', 'cd', 'de', 'ac', 'ad', 'ae', 'bd', 'be', 'ce']])]
    #[TestWith([3, ['abc', 'bcd', 'cde', 'abd', 'abe', 'ace', 'bce', 'ade', 'bde', 'acd']])]
    #[TestWith([4, ['abcd', 'bcde', 'abce', 'abde', 'acde', 'bced']])]
    #[TestWith([5, ['abcde']])]
    #[TestWith([10, ['abcde']])]
    public function keysCanReturnKeysInOrder(int $num, array $ordered): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        for ($i = 0; $i < 250; ++$i) {
            self::assertContains(\implode('', $this->randomizer->keys($array, $num, false)), $ordered);
        }
    }

    #[Test]
    #[TestWith([2, ['ab', 'bc', 'cd', 'de', 'ac', 'ad', 'ae', 'bd', 'be', 'ce']])]
    #[TestWith([3, ['abc', 'bcd', 'cde', 'abd', 'abe', 'ace', 'bce', 'ade', 'bde', 'cde']])]
    #[TestWith([4, ['abcd', 'bcde', 'abce', 'abde', 'acde', 'bced']])]
    #[TestWith([5, ['abcde']])]
    #[TestWith([10, ['abcde']])]
    public function keysCanReturnKeysShuffled(int $num, array $ordered): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        $success = false;
        for ($i = 0; $i < 250; ++$i) {
            if (! \in_array(\implode('', $this->randomizer->keys($array, $num, true)), $ordered, true)) {
                $success = true;
                break;
            }
        }

        self::assertTrue($success, 'Shuffled keys should produce different results');
    }

    #[Test]
    public function keysReturnsAtMostArrayLengthKeysInOrder(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5];
        $keys = $this->randomizer->keys($array, 10, false); // Do not shuffle
        self::assertSame(['a', 'b', 'c', 'd', 'e'], $keys);
    }

    #[Test]
    public function keysReturnsKeysPresentInOriginalArray(): void
    {
        $array = ['a' => 1, 'b' => 2, 'c' => 3];
        $num = 2;
        $keys = $this->randomizer->keys($array, $num);
        self::assertCount($num, $keys);
        foreach ($keys as $key) {
            self::assertArrayHasKey($key, $array);
        }
        // Ensure keys are unique (implicit in Randomizer::pickArrayKeys)
        self::assertSame($keys, \array_unique($keys));
    }

    #[Test]
    public function keysReturnsEmptyArrayForZeroNum(): void
    {
         $array = ['a' => 1, 'b' => 2];
         self::assertSame([], $this->randomizer->keys($array, 0));
    }

    #[Test]
    public function keysReturnsEmptyArrayForEmptyInputArray(): void
    {
        $array = [];
        self::assertSame([], $this->randomizer->keys($array, 2));
    }

    #[Test]
    public function keyReturnsKeyPresentInOriginalArray(): void
    {
        $array = ['x' => 10, 'y' => 20, 'z' => 30];
        $key = $this->randomizer->key($array);
        self::assertNotNull($key);
        self::assertArrayHasKey($key, $array);
    }

    #[Test]
    public function keyReturnsNullForEmptyArray(): void
    {
        $array = [];
        self::assertNull($this->randomizer->key($array));
    }

    #[Test]
    public function valueReturnsValuePresentInOriginalArray(): void
    {
        $array = ['one' => 'apple', 'two' => 'banana', 'three' => 'cherry'];
        $value = $this->randomizer->value($array);
        self::assertNotNull($value);
        self::assertContains($value, $array);
    }

    #[Test]
    public function valueReturnsNullForEmptyArray(): void
    {
        $array = [];
        self::assertNull($this->randomizer->value($array));
    }

    #[Test]
    public function valuesReturnsCorrectNumberOfValues(): void
    {
        $array = ['a' => 10, 'b' => 20, 'c' => 30, 'd' => 40, 'e' => 50];
        $num = 3;
        $values = $this->randomizer->values($array, $num); // Default preserve keys
        self::assertCount($num, $values);
    }

    #[Test]
    public function valuesReturnsValuesPresentInOriginalArrayPreservingKeys(): void
    {
        $array = ['a' => 10, 'b' => 20, 'c' => 30];
        $num = 2;
        $values = $this->randomizer->values($array, $num, true); // Preserve keys
        self::assertCount($num, $values);
        foreach ($values as $key => $value) {
            self::assertArrayHasKey($key, $array);
            self::assertSame($array[$key], $value);
        }
        // Ensure keys are unique (implicit in Randomizer::pickArrayKeys)
        self::assertSame($values, \array_intersect_key($values, \array_flip(\array_unique(\array_keys($values)))));
    }

     #[Test]
    public function valuesReturnsValuesPresentInOriginalArrayNotPreservingKeys(): void
    {
        $array = ['a' => 10, 'b' => 20, 'c' => 30];
        $num = 2;
        $values = $this->randomizer->values($array, $num, false); // Do not preserve keys
        self::assertCount($num, $values);
        /** @phpstan-ignore function.alreadyNarrowedType */
        self::assertTrue(\array_is_list($values));
        foreach ($values as $value) {
            self::assertContains($value, $array);
        }
        // Ensure values are unique if original values were unique (implicit in Randomizer::pickArrayKeys)
        self::assertSame($values, \array_unique($values));
    }

    #[Test]
    public function valuesUsesPreserveKeysTrueByDefault(): void
    {
        $array = ['a' => 10, 'b' => 20, 'c' => 30];
        $num = 2;
        $values = $this->randomizer->values($array, $num); // Default
        self::assertCount($num, $values);
        self::assertFalse(\array_is_list($values)); // Keys should be preserved by default
        foreach ($values as $key => $value) {
            self::assertArrayHasKey($key, $array);
            self::assertSame($array[$key], $value);
        }
    }

    #[Test]
    public function valuesReturnsEmptyArrayForZeroNum(): void
    {
         $array = ['a' => 1, 'b' => 2];
         self::assertSame([], $this->randomizer->values($array, 0));
    }

    #[Test]
    public function valuesReturnsEmptyArrayForEmptyInputArray(): void
    {
        $array = [];
        self::assertSame([], $this->randomizer->values($array, 2));
    }

    #[Test]
    public function hexReturnsCorrectLengthAndFormat(): void
    {
        $bytes_length = 8;
        $hex = $this->randomizer->hex($bytes_length);
        self::assertSame($bytes_length * 2, \strlen($hex));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $hex);
    }

    #[Test]
    public function hexThrowsExceptionForZeroBytes(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('bytes must be greater than 0');
        $this->randomizer->hex(0);
    }

    #[Test]
    public function hexThrowsExceptionForNegativeBytes(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('bytes must be greater than 0');
        $this->randomizer->hex(-5);
    }

    #[Test]
    public function enumReturnsValidCaseFromClass(): void
    {
        $result = $this->randomizer->enum(TestEnum::class);
        self::assertInstanceOf(TestEnum::class, $result);
        self::assertContains($result, TestEnum::cases());
    }

    #[Test]
    public function enumReturnsValidCaseFromInstance(): void
    {
        $result = $this->randomizer->enum(TestEnum::Foo);
        self::assertInstanceOf(TestEnum::class, $result);
        self::assertContains($result, TestEnum::cases());
    }

     #[Test]
    public function enumThrowsForNonEnumClassString(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Class stdClass is not a UnitEnum');
        // @phpstan-ignore-next-line argument.type (intentional invalid type for testing)
        $this->randomizer->enum(\stdClass::class);
    }

     #[Test]
    public function enumThrowsForEnumWithNoCases(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Enum has no cases');
        $this->randomizer->enum(EmptyEnum::class);
    }

    #[Test]
    public function weightedHappyPath(): void
    {
        for ($i = 0; $i < 100; ++$i) {
            $item = $this->randomizer->weighted(
                new WeightedItem(StoplightState::Green, 1),
                new WeightedItem(StoplightState::Yellow, 2),
                new WeightedItem(StoplightState::Red, 3),
            );

            self::assertContains($item, [StoplightState::Green, StoplightState::Yellow, StoplightState::Red]);

            $item = $this->randomizer->weighted(
                new WeightedItem(StoplightState::Green, 1),
                new WeightedItem(StoplightState::Yellow, 0),
                new WeightedItem(StoplightState::Red, 0),
            );

            self::assertSame(StoplightState::Green, $item);
        }
    }

    #[Test]
    public function weightedReturnsRandomWeightedValues(): void
    {
        $counter = new class (){
            public function __construct(
                public int $green = 0,
                public int $yellow = 0,
                public int $red = 0,
                public int $blue = 0,
            ) {
            }
        };

        $weights = [
            new WeightedItem('green', 100),
            new WeightedItem('yellow', 200),
            new WeightedItem('blue', 0),
            new WeightedItem('red', 300),
        ];

        $total = 100000;
        for ($i = 0; $i < $total; ++$i) {
            ++$counter->{$this->randomizer->weighted(...$weights)};
        }

        // Verify the distribution of items is within 1.25% of the expected weighted percentage
        self::assertEqualsWithDelta(\round(1 / 6, 4), \round($counter->green / $total, 4), 0.0125);
        self::assertEqualsWithDelta(\round(2 / 6, 4), \round($counter->yellow / $total, 4), 0.0125);
        self::assertEqualsWithDelta(\round(3 / 6, 4), \round($counter->red / $total, 4), 0.0125);
        self::assertSame(0, $counter->blue);
    }

    #[Test]
    public function weightedThrowsForEmptyArray(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('At least one weighted item is required total weight of items must be greater than 0');
        /** @phpstan-ignore-next-line (intentional empty array for testing) */
        $this->randomizer->weighted();
    }

    #[Test]
    public function weightedThrowsForZeroTotalWeight(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('At least one weighted item is required total weight of items must be greater than 0');
        $this->randomizer->weighted(
            new WeightedItem(StoplightState::Green, 0),
            new WeightedItem(StoplightState::Yellow, 0),
            new WeightedItem(StoplightState::Red, 0),
        );
    }
}
