<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use Crell\AttributeUtils\ClassAnalyzer;
use PhoneBurner\SaltLite\Attribute\AttributeAnalyzer;
use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Tests\Fixtures\ClassWithAttributes;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AttributeAnalyzerTest extends TestCase
{
    #[Test]
    public function hasReturnsTrueWhenAnalyzeReturnsObject(): void
    {
        $mockAnalyzer = $this->createMock(ClassAnalyzer::class);
        $mockAnalyzer->expects($this->once())
            ->method('analyze')
            ->willReturn(new \stdClass());

        $analyzer = new AttributeAnalyzer($mockAnalyzer);
        self::assertTrue($analyzer->has(ClassWithAttributes::class, Contract::class));
    }

    #[Test]
    public function hasReturnsFalseWhenAnalyzeThrowsException(): void
    {
        $mockAnalyzer = $this->createMock(ClassAnalyzer::class);
        $mockAnalyzer->expects($this->once())
            ->method('analyze')
            ->willThrowException(new \Exception('Analysis failed'));

        $analyzer = new AttributeAnalyzer($mockAnalyzer);
        self::assertFalse($analyzer->has(ClassWithAttributes::class, Contract::class));
    }

    #[Test]
    public function analyzeDelegatesToInnerAnalyzer(): void
    {
        $expected = new \stdClass();
        $mockAnalyzer = $this->createMock(ClassAnalyzer::class);
        $mockAnalyzer->expects($this->once())
            ->method('analyze')
            ->with(ClassWithAttributes::class, Contract::class, ['scope1'])
            ->willReturn($expected);

        $analyzer = new AttributeAnalyzer($mockAnalyzer);
        $result = $analyzer->analyze(ClassWithAttributes::class, Contract::class, ['scope1']);
        self::assertSame($expected, $result);
    }
}
