<?php

declare(strict_types=1);

namespace PamiModule\DIFactory\Service;

use PAMI\Client\IClient;
use PamiModule\Service\Client;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use function is_string;

final class ClientFactory
{
    private string $name;

    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }

    public function __invoke(ContainerInterface $container): Client
    {
        $config = $container->get('config') ?? [];
        $options = $config['pami']['client'][$this->name] ?? [];

        $connectionName = $options['connection'] ?? IClient::class;
        $connection = is_string($connectionName) && $container->has($connectionName)
            ? $container->get($connectionName)
            : null;

        if (! $connection instanceof IClient) {
            throw new \RuntimeException('Unable to find Pami Client service');
        }

        $eventDispatcherName = $options['event_dispatcher'] ?? EventDispatcherInterface::class;
        $eventDispatcher = is_string($eventDispatcherName) && $container->has($eventDispatcherName)
            ? $container->get($eventDispatcherName)
            : null;

        if (! $eventDispatcher instanceof EventDispatcherInterface) {
            throw new \RuntimeException('Unable to find an event dispatcher service');
        }

        return new Client($connection, $eventDispatcher);
    }

    /**
     * Allow serialization
     * @param array<string, mixed> $data
     * @return ClientFactory
     */
    public static function __set_state(array $data) : self
    {
        return new self(
            $data['name'] ?? 'default'
        );
    }
}
