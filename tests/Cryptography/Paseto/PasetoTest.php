<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Paseto;

use PhoneBurner\SaltLite\Cryptography\Paseto\Exception\PasetoCryptoException;
use PhoneBurner\SaltLite\Cryptography\Paseto\Paseto;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoPurpose;
use PhoneBurner\SaltLite\Cryptography\Paseto\PasetoVersion;
use PhoneBurner\SaltLite\Filesystem\File;
use PhoneBurner\SaltLite\Tests\Cryptography\Paseto\Protocol\Version1Test;
use PhoneBurner\SaltLite\Tests\Cryptography\Paseto\Protocol\Version2Test;
use PhoneBurner\SaltLite\Tests\Cryptography\Paseto\Protocol\Version3Test;
use PhoneBurner\SaltLite\Tests\Cryptography\Paseto\Protocol\Version4Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class PasetoTest extends TestCase
{
    #[Test]
    #[DataProvider('providesHappyPathTestCases')]
    public function happy_path(string $value, PasetoVersion $version, PasetoPurpose $purpose, string $footer): void
    {
        $token = new Paseto($value);

        self::assertSame($value, $token->value);
        self::assertSame($version, $token->version);
        self::assertSame($purpose, $token->purpose);
        self::assertSame($footer, $token->footer());
    }

    public static function providesHappyPathTestCases(): \Generator
    {
        foreach ([Version1Test::class, Version2Test::class, Version3Test::class, Version4Test::class] as $test_class) {
            $test_vectors = \json_decode(File::read($test_class::TEST_VECTOR_FILE), true, 512, \JSON_THROW_ON_ERROR);
            foreach ($test_vectors['tests'] as $test_vector) {
                if ($test_vector['expect-fail'] === false) {
                    $name = (string)$test_vector['name'];
                    yield $name => [
                        $test_vector['token'],
                        $test_class::VERSION,
                        $name[2] === 'E' ? PasetoPurpose::Local : PasetoPurpose::Public,
                        $test_vector['footer'] ?? '',
                    ];
                }
            }
        }
    }

    #[Test]
    #[DataProvider('providesSadPathTestCases')]
    public function sad_path(string $token): void
    {
        $this->expectException(PasetoCryptoException::class);
        $this->expectExceptionMessage('Invalid PASETO Token');
        new Paseto('invalid');
    }

    public static function providesSadPathTestCases(): \Generator
    {
        yield 'empty token' => [''];
        yield 'invalid token' => ['invalid'];
        yield 'invalid version_0' => ['v.local.eyJhbGciOiJFUzI1NiIsImtpZCI6IjEifQ.eyJwYXlsb2FkIjoiZm9vIn0'];
        yield 'invalid version_1' => ['v5.local.eyJhbGciOiJFUzI1NiIsImtpZCI6IjEifQ.eyJwYXlsb2FkIjoiZm9vIn0'];
        yield 'invalid_purpose' => ['v4.custom.eyJkYXRhIjoidGhpcyBpcyBhIHNpZ25lZCBtZXNzYWdlIiwiZXhwIjoiMjAyMi0wMS0wMVQwMDowMDowMCswMDowMCJ9bg_XBBzds8lTZShVlwwKSgeKpLT3yukTw6JUz3W4h_ExsQV-P0V54zemZDcAxFaSeef1QlXEFtkqxT1ciiQEDA'];
        yield 'invalid_base64url_0' => ['v4.local.eyJkYXRhIjoidGhpcyBpcyBhIHNpZ25lZCBtZXNzYWdlIiwiZXhwIjoiMjAyMi0wMS0wMVQwMDowMDowMCswMDowMCJ9bg+XBBzds8lTZShVlwwKSgeKpLT3yukTw6JUz3W4h_ExsQV-P0V54zemZDcAxFaSeef1QlXEFtkqxT1ciiQEDA'];
        yield 'invalid_base64url_1' => ['v4.local.eyJkYXRhIjoidGhpcyBpcyBhIHNpZ25lZCBtZXNzYWdlIiwiZXhwIjoiMjAyMi0wMS0wMVQwMDowMDowMCswMDowMCJ9bg_XBBzds8lTZShVlwwKSgeKpLT3yukTw6JUz3W4h_ExsQV-P0V54zemZDcAxFaSeef1QlXEFtkqxT1ciiQEDA=='];
        yield 'invalid_base64url_2' => ['v4.local.eyJkYXRhIjoidGhpcyBpcyBhIHNpZ25lZCBtZXNzYWdlIiwiZXhwIjoiMjAyMi0wMS0wMVQwMDowMDowMCswMDowMCJ9bg_XBBzds8lTZShVlwwKSgeKpLT3yukTw6JUz3W4h_ExsQV-P0V54zemZDcAxFaSeef1QlXEFtkqxT1ciiQEDA.BtZXNzYWdlIiwiZXh=='];
    }
}
