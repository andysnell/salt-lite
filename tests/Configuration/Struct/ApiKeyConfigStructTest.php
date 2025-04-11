<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Configuration\Struct;

use PhoneBurner\SaltLite\Tests\Configuration\Struct\TestApiKeyConfigStruct;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ApiKeyConfigStructTest extends TestCase
{
    #[Test]
    public function constructorSetsApiKey(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->assertSame('test-api-key', $config->api_key);
    }

    #[Test]
    public function constructorSetsNullApiKey(): void
    {
        $config = new TestApiKeyConfigStruct(null);
        $this->assertNull($config->api_key);
    }

    #[Test]
    public function constructorSetsEmptyStringToNull(): void
    {
        $config = new TestApiKeyConfigStruct('');
        $this->assertNull($config->api_key);
    }

    #[Test]
    public function arrayAccessOffsetExists(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->assertArrayHasKey('api_key', $config);
        $this->assertArrayNotHasKey('non_existent', $config);
    }

    #[Test]
    public function arrayAccessOffsetGet(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->assertSame('test-api-key', $config['api_key']);
    }

    #[Test]
    public function arrayAccessOffsetGetReturnsNullForNonExistentKey(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->assertNull($config['non_existent']);
    }

    #[Test]
    public function arrayAccessOffsetSetThrowsException(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Config Structs are Immutable');
        $config['api_key'] = 'new-key';
    }

    #[Test]
    public function arrayAccessOffsetUnsetThrowsException(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Config Structs are Immutable');
        unset($config['api_key']);
    }

    #[Test]
    public function serialization(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $serialized = \serialize($config);
        $unserialized = \unserialize($serialized);
        $this->assertEquals($config, $unserialized);
    }

    #[Test]
    public function jsonSerialization(): void
    {
        $config = new TestApiKeyConfigStruct('test-api-key');
        $json = \json_encode($config);
        $this->assertSame('{"api_key":"test-api-key"}', $json);
    }
}
