<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionKeyPair;
use PhoneBurner\SaltLite\Cryptography\Defaults;
use PhoneBurner\SaltLite\Cryptography\KeyManagement\KeyChain;
use PhoneBurner\SaltLite\Cryptography\Natrium;
use PhoneBurner\SaltLite\Cryptography\String\Ciphertext;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SharedKey;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SymmetricAlgorithm;
use PhoneBurner\SaltLite\Filesystem\File;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class NatriumTest extends TestCase
{
    public const string MESSAGE = 'The Quick Brown Fox Jumps Over The Lazy Dog';

    public const string KNOWN_KEY = 'pP8fF46Eb737WAN9ccW1iZJP3w/7GESMKgfWT38/aU0=';

    public const string KNOWN_SENDER_KEYPAIR = 'kk72c6s2di5fKvBXLSbYCISOvj+a26p3nhe/+TzTi3osLpeqgv2ChN/RzsZskMYLU7jct02PprzdoHPeUwt5Kg==';

    public const string KNOWN_RECIPIENT_KEYPAIR = 'fvVzvZ085EQ+chb5HtMzBhLcBHjVAQi1g4CnQfuJnjTGPBGm6sIenWqy7v7b4iNdaQhtpn6gDVtpXquKyo7KKQ==';

    #[Test]
    public function symmetricRegressionTestDefault(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $ciphertext = CipherText::import(File::read(__DIR__ . '/Fixtures/lorem_aegis256.txt'));

        $natrium = new Natrium(new KeyChain($shared_key));

        $plaintext = $natrium->decryptWithSharedKey($ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);

        $ciphertext = $natrium->encryptWithSharedKey($plaintext);
        $plaintext = $natrium->decryptWithSharedKey($ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);
    }

    #[Test]
    public function symmetricRegressionTestAlternateAlgo(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $ciphertext = CipherText::import(File::read(__DIR__ . '/Fixtures/lorem_xchacha20blake2b.txt'));

        $natrium = new Natrium(new KeyChain($shared_key), defaults: new Defaults(symmetric: SymmetricAlgorithm::XChaCha20Blake2b));

        $plaintext = $natrium->decryptWithSharedKey($ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);

        $ciphertext = $natrium->encryptWithSharedKey($plaintext);
        $plaintext = $natrium->decryptWithSharedKey($ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);
    }

    #[Test]
    public function asymmetricRegressionTestDefault(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $recipient_keypair = EncryptionKeyPair::import(self::KNOWN_RECIPIENT_KEYPAIR);
        $sender_keypair = EncryptionKeyPair::import(self::KNOWN_SENDER_KEYPAIR);

        $ciphertext = CipherText::import(File::read(__DIR__ . '/Fixtures/lorem_X25519aegis256.txt'));

        $natrium = new Natrium(new KeyChain($shared_key, $recipient_keypair));

        $plaintext = $natrium->decryptWithSecretKey($sender_keypair->public, $ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);

        // not something you'd actually do, but for the sake of testing...
        $ciphertext = $natrium->encryptWithPublicKey($recipient_keypair->public, $plaintext);
        $plaintext = $natrium->decryptWithSecretKey($recipient_keypair->public, $ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);
    }

    #[Test]
    public function asymmetricRegressionTestAlternateAlgo(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $recipient_keypair = EncryptionKeyPair::import(self::KNOWN_RECIPIENT_KEYPAIR);
        $sender_keypair = EncryptionKeyPair::import(self::KNOWN_SENDER_KEYPAIR);

        $ciphertext = CipherText::import(File::read(__DIR__ . '/Fixtures/lorem_x25519xchacha20blake2b.txt'));

        $natrium = new Natrium(
            keys: new KeyChain($shared_key, $recipient_keypair),
            defaults: new Defaults(asymmetric: AsymmetricAlgorithm::X25519XChaCha20Blake2b),
        );

        $plaintext = $natrium->decryptWithSecretKey($sender_keypair->public, $ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);

        // not something you'd actually do, but for the sake of testing...
        $ciphertext = $natrium->encryptWithPublicKey($recipient_keypair->public, $plaintext);
        $plaintext = $natrium->decryptWithSecretKey($recipient_keypair->public, $ciphertext);

        self::assertNotNull($plaintext);
        self::assertStringEqualsFile(__DIR__ . '/Fixtures/lorem.txt', $plaintext);
    }

    #[Test]
    public function happyPathForSymmetricSignatures(): void
    {
        $natrium = new Natrium(new KeyChain(SharedKey::import(self::KNOWN_KEY)));

        $signature = $natrium->signWithSharedKey(self::MESSAGE);

        self::assertTrue($natrium->verifyWithSharedKey(self::MESSAGE, $signature));
    }

    #[Test]
    public function happyPathForAsymmetricSignatures(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $natrium = new Natrium(new KeyChain($shared_key));

        $signature = $natrium->signWithSecretKey(self::MESSAGE);

        self::assertTrue($natrium->verifyWithPublicKey($natrium->keys->signature()->public, $signature, self::MESSAGE));
    }

    #[Test]
    public function happyPathForEncryptedPaseto(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $natrium = new Natrium(new KeyChain($shared_key));

        $paseto = $natrium->encryptPaseto(
            'Foo Bar',
            'auth.salt-lite.example.com',
            'app.salt-lite.example.com',
        );

        $claims = $natrium->decryptPaseto($paseto);

        self::assertNotNull($claims);
        self::assertSame('Foo Bar', $claims->payload->sub);
        self::assertSame('auth.salt-lite.example.com', $claims->payload->iss);
        self::assertSame('app.salt-lite.example.com', $claims->payload->aud);
        self::assertTrue($natrium->validatePaseto($claims));
    }

    #[Test]
    public function happyPathForSignedPaseto(): void
    {
        $shared_key = SharedKey::import(self::KNOWN_KEY);

        $natrium = new Natrium(new KeyChain($shared_key));

        $paseto = $natrium->signPaseto(
            'Foo Bar',
            'auth.salt-lite.example.com',
            'app.salt-lite.example.com',
        );

        $claims = $natrium->verifyPaseto($paseto);

        self::assertNotNull($claims);
        self::assertSame('Foo Bar', $claims->payload->sub);
        self::assertSame('auth.salt-lite.example.com', $claims->payload->iss);
        self::assertSame('app.salt-lite.example.com', $claims->payload->aud);
        self::assertTrue($natrium->validatePaseto($claims));
    }
}
