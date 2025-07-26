<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\App\Event;

use PhoneBurner\SaltLite\App\Event\KernelExecutionStart;
use PhoneBurner\SaltLite\App\Kernel;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class KernelExecutionStartTest extends TestCase
{
    #[Test]
    public function constructorSetsKernelProperty(): void
    {
        $kernel = $this->createMock(Kernel::class);
        self::assertSame($kernel, new KernelExecutionStart($kernel)->kernel);
    }
}
