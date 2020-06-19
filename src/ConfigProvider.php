<?php

declare(strict_types=1);

namespace PamiModule;

use PAMI\Client\IClient;
use PAMI\Client\Impl\ClientImpl;
use PamiModule\DIFactory\Service\ClientFactory;
use PamiModule\DIFactory\Service\ConnectionFactory;
use PamiModule\Service\Client;
use PamiModule\Service\ClientInterface;

class ConfigProvider
{
    /**
     * Provide dependency configuration for an application integrating i18n.
     *
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
        ];
    }
    /**
     * Provide dependency configuration for an application integrating i18n.
     *
     * @return array<string, mixed>
     */
    public function getDependencies(): array
    {
        return [
            'aliases' => [
                IClient::class => ClientImpl::class,
                ClientInterface::class => Client::class,
            ],
            'factories' => [
                ClientImpl::class => ConnectionFactory::class,
                Client::class => ClientFactory::class,
            ],
        ];
    }
}
