<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Attribute;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\Algorithm\X25519XChaCha20Poly1305;
use PhoneBurner\SaltLite\Cryptography\Attribute\AlgorithmImplementation;
use PhoneBurner\SaltLite\Cryptography\Symmetric\Algorithm\XChaCha20Blake2b;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AlgorithmImplementationTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $symmetric = new XChaCha20Blake2b();
        $sut = new AlgorithmImplementation($symmetric);
        self::assertSame($symmetric, $sut->algorithm);

        $asymmetric = new X25519XChaCha20Poly1305();
        $sut = new AlgorithmImplementation($asymmetric);
        self::assertSame($asymmetric, $sut->algorithm);
    }
}
