<?php

namespace PamiModule\Service;

use PamiModule\Listener\ConnectionStatusListener;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConnectionStatusDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service.
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string                  $name           the normalized service name
     * @param string                  $requestedName  the requested service name
     * @param callable                $callback       the callback that is responsible for creating the service
     *
     * @return \PamiModule\Service\Client
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var \PamiModule\Service\Client $client */
        $client = $callback();

        $listener = new ConnectionStatusListener();
        $client->getEventManager()->attachAggregate($listener);

        return $client;
    }
}
