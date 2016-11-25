<?php

namespace PamiModule\Service;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use PAMI\Client\Impl\ClientImpl;
use PamiModule\Event\EventForwarder;
use PamiModule\Options\Client as ClientOptions;
use PamiModule\Options\Connection;
use Zend\EventManager\EventManager;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClientFactory.
 */
class ClientFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return \PamiModule\Options\Client::class;
    }

    /**
     * Create an object.
     *
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param null|array         $options
     *
     * @throws \RuntimeException
     * @throws \Interop\Container\Exception\NotFoundException
     * @throws ServiceNotFoundException                       if unable to resolve the service
     * @throws ServiceNotCreatedException                     if an exception is raised when
     *                                                        creating a service
     * @throws ContainerException                             if any other error occurs
     *
     * @return Client
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = null;
        $connectionName = $this->getName();

        if ($this->hasOptions($container, 'client', $this->getName())) {
            /** @var ClientOptions $options */
            $options = $this->getOptions($container, 'client');
            $connectionName = $options->getConnection() ?: $this->getName();
        }

        $connectionFactory = new ConnectionFactory($connectionName);
        /** @var Connection $connectionOptions */
        $connectionOptions = $connectionFactory->getOptions($container, 'connection');

        /** @var ClientImpl $connection */
        $connection = $container->get(sprintf('pami.connection.%s', $connectionName));

        $client = new Client($connectionOptions->getHost(), $connection);
        $client->setEventManager($this->createEventManager($container));

        if ($options) {
            $client->setParams($options->getParams());
        }

        $eventForwarder = new EventForwarder($client);
        $client->getConnection()->registerEventListener($eventForwarder);

        return $client;
    }

    /**
     * @param ContainerInterface $container
     *
     * @return EventManager
     */
    protected function createEventManager(ContainerInterface $container)
    {
        if ($this->acceptsSharedManagerToConstructor()) {
            // zend-eventmanager v3
            return new EventManager(
                $container->has('SharedEventManager') ? $container->get('SharedEventManager') : null
            );
        }

        // zend-eventmanager v2
        $events = new EventManager();

        if ($container->has('SharedEventManager')) {
            $events->setSharedManager($container->get('SharedEventManager'));
        }

        return $events;
    }

    /**
     * Does the EventManager accept the shared manager to the constructor?
     *
     * In zend-eventmanager v3, the EventManager accepts the shared manager
     * instance to the constructor *only*, while in v2, it must be injected
     * via the setSharedManager() method.
     *
     * @return bool
     */
    private function acceptsSharedManagerToConstructor()
    {
        $r = new \ReflectionClass(EventManager::class);

        return !$r->hasMethod('setSharedManager');
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     *
     * @return Client
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, Client::class);
    }
}
