<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ObjectContainer;

use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PhoneBurner\SaltLite\Container\ObjectContainer\ImmutableObjectContainer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ImmutableObjectContainerTest extends TestCase
{
    #[Test]
    public function happyPathTests(): void
    {
        $a = new \stdClass();
        $b = new \stdClass();
        $c = new \stdClass();

        $container = new ImmutableObjectContainer([
            'a' => $a,
            'b' => $b,
            'c' => $c,
        ]);

        self::assertCount(3, $container);
        self::assertSame($a, $container->get('a'));
        self::assertSame($b, $container->get('b'));
        self::assertSame($c, $container->get('c'));

        self::assertTrue($container->has('a'));
        self::assertTrue($container->has('b'));
        self::assertTrue($container->has('c'));

        self::assertSame(['a', 'b', 'c'], $container->keys());

        self::assertSame(['a' => $a, 'b' => $b, 'c' => $c], [...$container]);

        /** @phpstan-ignore argument.type (generics expectations differ, possible phpstan bug) */
        self::assertArrayHasKey('a', $container);
        self::assertSame($a, $container['a']);

        self::assertSame($b, $container->call(fn(): \stdClass => $b));
    }

    #[Test]
    public function throwsExceptionWhenKeyNotFound(): void
    {
        $container = new ImmutableObjectContainer([]);
        self::assertEmpty($container);
        self::assertSame([], $container->keys());
        self::assertSame([], [...$container]);
        self::assertFalse($container->has('a'));

        $this->expectException(NotFound::class);
        $container->get('a');
    }

    #[Test]
    public function throwsExceptionWhenSettingThroughArrayAccess(): void
    {
        $container = new ImmutableObjectContainer([]);

        $this->expectException(\LogicException::class);
        /** @phpstan-ignore-next-line */
        $container['a'] = new \stdClass();
    }

    #[Test]
    public function throwsExceptionWhenUnsettingThroughArrayAccess(): void
    {
        $container = new ImmutableObjectContainer([
            'a' => new \stdClass(),
        ]);

        $this->expectException(\LogicException::class);
        /** @phpstan-ignore-next-line */
        unset($container['a']);
    }
}
