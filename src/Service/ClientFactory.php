<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use PamiModule\Options\Client as ClientOptions;
use PamiModule\Event\EventForwarder;
use Zend\EventManager\EventManager;
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
        $options = null;
        $connectionName = $this->getName();

        if ($this->hasOptions($serviceLocator, 'client', $this->getName())) {
            /** @var ClientOptions $options */
            $options = $this->getOptions($serviceLocator, 'client');
            $connectionName = $options->getConnection() ?: $this->getName();
        }

        /** @var ClientImpl $connection */
        $connection = $serviceLocator->get(sprintf('pami.connection.%s', $connectionName));

        $eventManager = new EventManager();

        $client = new Client($connection, $eventManager);
        $eventManager->setIdentifiers(get_class($client));

        if ($options) {
            $client->setParams($options->getParams());
        }

        $eventForwarder = new EventForwarder($client);
        $client->getConnection()->registerEventListener($eventForwarder);

        return $client;
    }
}
