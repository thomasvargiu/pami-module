<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use PamiModule\Options\Client as ClientOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class ClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'PamiModule\\Options\\Client';
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var ClientOptions $options */
        $options = $this->getOptions($serviceLocator, 'client');

        $connectionName = $options->getConnection() ?: $this->getName();

        /** @var ClientImpl $connection */
        $connection = $serviceLocator->get(sprintf('pami.connection.%s', $connectionName));

        $client = new Client($connection);
        $client->setParams($options->getParams());

        return $client;
    }
}
