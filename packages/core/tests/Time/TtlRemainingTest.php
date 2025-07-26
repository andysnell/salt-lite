<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time;

use PhoneBurner\SaltLite\Time\TimeConstant;
use PhoneBurner\SaltLite\Time\Ttl;
use PhoneBurner\SaltLite\Time\TtlRemaining;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class TtlRemainingTest extends TestCase
{
    #[Test]
    public function sutCanBeInstantiatedWithMinTtlRemaining(): void
    {
        self::assertSame(0, TtlRemaining::min()->seconds);
    }

    #[DataProvider('provideSeconds')]
    #[Test]
    public function sutCanBeInstantiatedWithSeconds(int|float $expected, int|float $seconds): void
    {
        self::assertSame($expected, new TtlRemaining($seconds)->seconds);
        self::assertSame($expected, TtlRemaining::seconds($seconds)->seconds);
    }

    public static function provideSeconds(): \Generator
    {
        yield [0, 0];
        yield [0.0, 0.0];
        yield [3 * TimeConstant::SECONDS_IN_MINUTE, 3 * TimeConstant::SECONDS_IN_MINUTE];
        yield [0.1234, 0.1234];
        yield [\PHP_INT_MAX, \PHP_INT_MAX];
        yield [\PHP_INT_MAX + 1, \PHP_INT_MAX + 1];
    }

    #[DataProvider('provideMinutes')]
    #[Test]
    public function sutCanBeInstantiatedWithMinutes(int|float $expected, int|float $minutes): void
    {
        self::assertSame($expected, TtlRemaining::minutes($minutes)->seconds);
    }

    public static function provideMinutes(): \Generator
    {
        yield [0, 0];
        yield [0.0, 0.0];
        yield [5 * TimeConstant::SECONDS_IN_MINUTE, 5];
        yield [0.1234 * TimeConstant::SECONDS_IN_MINUTE, 0.1234];
        yield [\PHP_INT_MAX * TimeConstant::SECONDS_IN_MINUTE, \PHP_INT_MAX];
    }

    #[DataProvider('provideHours')]
    #[Test]
    public function sutCanBeInstantiatedWithHours(int $expected, int $hours): void
    {
        self::assertSame($expected, TtlRemaining::hours($hours)->seconds);
    }

    public static function provideHours(): \Generator
    {
        yield [0, 0];
        yield [TimeConstant::SECONDS_IN_HOUR, 1];
        yield [5 * TimeConstant::SECONDS_IN_HOUR, 5];
        yield [24 * TimeConstant::SECONDS_IN_HOUR, 24];
    }

    #[DataProvider('provideDays')]
    #[Test]
    public function sutCanBeInstantiatedWithDays(int $expected, int $days): void
    {
        self::assertSame($expected, TtlRemaining::days($days)->seconds);
    }

    public static function provideDays(): \Generator
    {
        yield [0, 0];
        yield [TimeConstant::SECONDS_IN_DAY, 1];
        yield [7 * TimeConstant::SECONDS_IN_DAY, 7];
        yield [31 * TimeConstant::SECONDS_IN_DAY, 31];
    }

    #[DataProvider('provideDateTimeInterfaces')]
    #[Test]
    public function sutCanBeInstantiatedBasedOnDatetime(
        int|float $expected,
        \DateTimeInterface $datetime,
        \DateTimeInterface $now,
    ): void {
        self::assertSame($expected, TtlRemaining::until($datetime, $now)->seconds);
    }

    public static function provideDateTimeInterfaces(): \Generator
    {
        $now = new \DateTimeImmutable();
        yield [0.0, $now, $now];
        yield [3600.0, $now->add(new \DateInterval('PT1H')), $now];
        yield [3782.0, $now->add(new \DateInterval('PT1H3M2S')), $now];
        yield [0.0, new \DateTimeImmutable('@0'), new \DateTimeImmutable('@0')];
    }

    #[DataProvider('provideInvalidSeconds')]
    #[Test]
    public function timeToLiveCannotBeNegative(int|float $seconds): void
    {
        $this->expectException(\UnexpectedValueException::class);
        new TtlRemaining($seconds);
    }

    public static function provideInvalidSeconds(): \Generator
    {
        yield [-1];
        yield [-0.1];
        yield [\PHP_INT_MIN];
        yield [(float)\PHP_INT_MIN];
    }

    #[DataProvider('provideValidMakeTestCases')]
    #[Test]
    public function makeReturnsExpectedTtlRemaining(
        mixed $input,
        TtlRemaining $expected,
        \DateTimeImmutable|null $now = null,
    ): void {
        self::assertEquals($expected, TtlRemaining::make($input, $now ?? new \DateTimeImmutable()));
    }

    public static function provideValidMakeTestCases(): \Generator
    {
        $datetime = new \DateTimeImmutable();

        yield [TtlRemaining::seconds(42), TtlRemaining::seconds(42)];
        yield [Ttl::seconds(42), TtlRemaining::seconds(42)];
        yield [new \DateInterval('PT1H'), TtlRemaining::seconds(3600)];
        yield [new \DateInterval('PT1H42S'), TtlRemaining::seconds(3642)];
        yield [new \DateTimeImmutable('@0'), TtlRemaining::seconds(0), new \DateTimeImmutable('@0')];
        yield [$datetime->add(new \DateInterval('PT1H42S')), TtlRemaining::seconds(3642), $datetime];
        yield [0, TtlRemaining::min()];
        yield [0.0, TtlRemaining::min()];
        yield [0.1234, TtlRemaining::seconds(0.1234)];
        yield [1, TtlRemaining::seconds(1)];
        yield [12345, TtlRemaining::seconds(12345)];
        yield [12345.67, TtlRemaining::seconds(12345.67)];
        yield ['0', TtlRemaining::min()];
        yield ['0.0', TtlRemaining::min()];
        yield ['0.1234', TtlRemaining::seconds(0.1234)];
        yield ['1', TtlRemaining::seconds(1)];
        yield ['12345', TtlRemaining::seconds(12345)];
        yield ['12345.67', TtlRemaining::seconds(12345.67)];
    }

    #[DataProvider('provideInvalidMakeTestCases')]
    #[Test]
    public function makeThrowsExceptionWithInvalidInput(mixed $input): void
    {
        $this->expectException(\UnexpectedValueException::class);
        TtlRemaining::make($input);
    }

    public static function provideInvalidMakeTestCases(): \Generator
    {
        yield [-1];
        yield [-0.1];
        yield [\PHP_INT_MIN];
        yield [(float)\PHP_INT_MIN];
        yield ['foo'];
        yield [['foo' => 'bar']];
        yield [[]];
        yield [new \stdClass()];
    }
}
