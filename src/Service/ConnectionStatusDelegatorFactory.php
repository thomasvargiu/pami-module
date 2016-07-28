<?php

namespace PamiModule\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PamiModule\Listener\ConnectionStatusListener;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConnectionStatusDelegatorFactory
 *
 * @package PamiModule\Service
 */
class ConnectionStatusDelegatorFactory implements DelegatorFactoryInterface
{

    /**
     * A factory that creates delegates of a given service
     *
     * @param  ContainerInterface $container
     * @param  string             $name
     * @param  callable           $callback
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $name, callable $callback, array $options = null)
    {
        /** @var \PamiModule\Service\Client $client */
        $client = $callback();

        $listener = $container->get(ConnectionStatusListener::class);
        $listener->attach($client->getEventManager(), 10000);

        return $client;
    }

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
        return $this($serviceLocator, $requestedName, $callback);
    }
}
