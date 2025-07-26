<?php

declare(strict_types=1);

namespace App\Tests\Unit\TestSupport;

use PhoneBurner\SaltLite\Configuration\ConfigStruct;
use PhoneBurner\SaltLite\Framework\MessageBus\Container\MessageBusContainer;
use PhoneBurner\SaltLite\Framework\MessageBus\TransportFactory;
use PhoneBurner\SaltLite\MessageBus\MessageBus;
use PhoneBurner\SaltLite\Type\Type;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Transport\Sync\SyncTransport;
use Symfony\Component\Messenger\Transport\TransportFactory as SymfonyTransportFactory;
use Symfony\Component\Messenger\Transport\TransportInterface;

class ForceSyncTransportFactory extends SymfonyTransportFactory implements TransportFactory
{
    public function __construct(private readonly MessageBusContainer $message_bus_locator)
    {
    }

    #[\Override]
    public function make(ConfigStruct|array $config): TransportInterface
    {
        return new SyncTransport(
            Type::of(MessageBusInterface::class, $this->message_bus_locator->get($config['bus'] ?? MessageBus::DEFAULT)),
        );
    }
}
