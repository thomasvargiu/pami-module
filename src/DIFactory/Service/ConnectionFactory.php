<?php

declare(strict_types=1);

namespace PamiModule\DIFactory\Service;

use PAMI\Client\Impl\ClientImpl;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use function count;
use function implode;
use function is_array;
use function is_string;

final class ConnectionFactory
{
    private string $name;

    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }

    public function __invoke(ContainerInterface $container): ClientImpl
    {
        $config = $container->get('config') ?? [];
        $options = $config['pami']['connection'][$this->name] ?? [];

        $eventMask = $options['event_mask'] ?? [];

        if (is_array($eventMask) && 0 === count($eventMask)) {
            $eventMask = 'off';
        } elseif (is_array($eventMask)) {
            $eventMask = implode(', ', $eventMask);
        }

        $logger = null;
        if (is_string($options['logger'] ?? null)) {
            $logger = $container->get($options['logger']);
        }

        $clientOptions = [
            'host' => $options['host'] ?? 'localhost',
            'port' => (int) ($options['port'] ?? 5038),
            'username' => $options['username'] ?? '',
            'secret' => $options['secret'] ?? '',
            'connect_timeout' => (int) ($options['connect_timeout'] ?? 10000),
            'read_timeout' => (int) ($options['read_timeout'] ?? 10000),
            'scheme' => $options['scheme'] ?? 'tcp://',
            'event_mask' => $eventMask,
        ];

        /** @phpstan-ignore-next-line */
        $client = new ClientImpl($clientOptions);

        if ($logger instanceof LoggerInterface) {
            $client->setLogger($logger);
        }

        return $client;
    }

    /**
     * Allow serialization
     * @param array<string, mixed> $data
     * @return ConnectionFactory
     */
    public static function __set_state(array $data) : self
    {
        return new self(
            $data['name'] ?? 'default'
        );
    }
}
