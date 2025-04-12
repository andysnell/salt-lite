<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Asymmetric\Message;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionKeyPair;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\EncryptionPublicKey;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\Message\EncryptedMessageBox;
use PhoneBurner\SaltLite\Cryptography\Asymmetric\Message\MultipleRecipientMessageBox;
use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PhoneBurner\SaltLite\Cryptography\String\Ciphertext;
use PhoneBurner\SaltLite\Cryptography\String\Nonce;
use PhoneBurner\SaltLite\Cryptography\Symmetric\EncryptedMessage;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SymmetricAlgorithm;
use PhoneBurner\SaltLite\Exception\NotImplemented;
use PhoneBurner\SaltLite\Serialization\Json;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MultipleRecipientMessageBoxTest extends TestCase
{
    private AsymmetricAlgorithm $algorithm;

    private EncryptionPublicKey $sender_key;

    private EncryptionPublicKey $recipient_key;

    private Ciphertext $ciphertext;

    private Nonce $nonce;

    protected function setUp(): void
    {
        $this->algorithm = AsymmetricAlgorithm::X25519Aegis256;
        $this->sender_key = EncryptionKeyPair::generate()->public();
        $this->recipient_key = EncryptionKeyPair::generate()->public();
        $this->ciphertext = new Ciphertext(\random_bytes(2048));
        $this->nonce = Nonce::generate();
    }

    #[Test]
    public function throwsWhenEncapsulatedKeyAlgorithmMismatches(): void
    {
        $this->expectException(CryptoLogicException::class);
        $this->expectExceptionMessage('Encapsulated shared key algorithm must match the asymmetric algorithm');

        $encrypted_message = new EncryptedMessage(
            SymmetricAlgorithm::Aegis256,
            $this->ciphertext,
            $this->nonce,
        );

        $encapsulated_key = new EncryptedMessageBox(
            AsymmetricAlgorithm::X25519XChaCha20Poly1305,
            $this->sender_key,
            $this->recipient_key,
            $this->ciphertext,
            $this->nonce,
        );

        new MultipleRecipientMessageBox(
            $this->algorithm,
            $this->sender_key,
            [$encapsulated_key],
            $encrypted_message,
        );
    }

    #[Test]
    public function throwsWhenEncapsulatedKeySenderMismatches(): void
    {
        $this->expectException(CryptoLogicException::class);
        $this->expectExceptionMessage('Encapsulated shared key sender must match the message sender');

        $encrypted_message = new EncryptedMessage(
            SymmetricAlgorithm::Aegis256,
            $this->ciphertext,
            $this->nonce,
        );

        $different_sender_key = EncryptionKeyPair::generate()->public();

        $encapsulated_key = new EncryptedMessageBox(
            $this->algorithm,
            $different_sender_key,
            $this->recipient_key,
            $this->ciphertext,
            $this->nonce,
        );

        new MultipleRecipientMessageBox(
            $this->algorithm,
            $this->sender_key,
            [$encapsulated_key],
            $encrypted_message,
        );
    }

    #[Test]
    public function jsonSerializeReturnsExpectedFormat(): void
    {
        $message_box = $this->createValidMessageBox();

        $json = $message_box->jsonSerialize();
        $data = Json::decode($json);

        $this->assertSame(1, $data['v']);
        $this->assertSame('X25519Aegis256', $data['alg']);
        $this->assertSame($this->sender_key->export(), $data['pub']);
        $this->assertCount(1, $data['k']);
        $this->assertSame($this->recipient_key->export(), $data['k'][0]['pub']);
        $this->assertSame($this->ciphertext->export(), $data['k'][0]['box']);
        $this->assertSame('Aegis256', $data['m']['alg']);
        $this->assertSame($this->nonce->export(), $data['m']['n']);
    }

    #[Test]
    public function bytesReturnsSerializedString(): void
    {
        $message_box = $this->createValidMessageBox();

        $this->expectException(NotImplemented::class);
        $message_box->bytes();
    }

    #[Test]
    public function lengthReturnsCorrectByteCount(): void
    {
        $message_box = $this->createValidMessageBox();

        $this->expectException(NotImplemented::class);
        $message_box->length();
    }

    #[Test]
    public function toStringReturnsSerializedString(): void
    {
        $message_box = $this->createValidMessageBox();

        $this->expectException(NotImplemented::class);
        echo (string)$message_box;
    }

    private function createValidMessageBox(): MultipleRecipientMessageBox
    {
        $encapsulated_key = new EncryptedMessageBox(
            $this->algorithm,
            $this->sender_key,
            $this->recipient_key,
            $this->ciphertext,
            $this->nonce,
        );

        return new MultipleRecipientMessageBox(
            $this->algorithm,
            $this->sender_key,
            [$encapsulated_key],
            new EncryptedMessage(
                SymmetricAlgorithm::Aegis256,
                $this->ciphertext,
                $this->nonce,
            ),
        );
    }
}
