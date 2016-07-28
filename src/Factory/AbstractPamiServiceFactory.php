<?php

namespace PamiModule\Factory;

use Interop\Container\ContainerInterface;
use Interop\Container\Exception\ContainerException;
use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractPamiServiceFactory
 *
 * @package PamiModule\Factory
 */
class AbstractPamiServiceFactory implements AbstractFactoryInterface
{
    /**
     * Can the factory create an instance for the service?
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @return bool
     */
    public function canCreate(ContainerInterface $container, $requestedName)
    {
        return false !== $this->getFactoryMapping($container, $requestedName);
    }

    /**
     * Create an object
     *
     * @param  ContainerInterface $container
     * @param  string             $requestedName
     * @param  null|array         $options
     * @return object
     * @throws ServiceNotFoundException if unable to resolve the service.
     * @throws ServiceNotCreatedException if an exception is raised when
     *     creating a service.
     * @throws ContainerException if any other error occurs
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $mappings = $this->getFactoryMapping($container, $requestedName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var $factory \PamiModule\Service\AbstractFactory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory($container, \PamiModule\Service\AbstractFactory::class);
    }

    /**
     * Determine if we can create a service with name.
     *
     * @param ServiceLocatorInterface $serviceLocator Service Locator
     * @param string                  $name           Service name
     * @param string                  $requestedName  Service name requested
     *
     * @return bool
     */
    public function canCreateServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName
    ) {
        return $this->canCreate($serviceLocator, $requestedName);
    }

    /**
     * Get the factoring map.
     *
     * @param ContainerInterface $container Service locator
     * @param string             $name      Service name
     *
     * @return bool|array
     */
    private function getFactoryMapping(ContainerInterface $container, $name)
    {
        $matches = [];

        if (!preg_match('/^pami\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $container->get('config');
        $serviceType = $matches['serviceType'];
        $serviceName = $matches['serviceName'];

        if (!isset($config['pami_module_factories'][$serviceType]) ||
            !isset($config['pami_module'][$serviceType][$serviceName])) {
            return false;
        }

        return [
            'serviceType' => $serviceType,
            'serviceName' => $serviceName,
            'factoryClass' => $config['pami_module_factories'][$serviceType],
        ];
    }

    /**
     * Create service with name.
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     * @param string                  $name           Service name
     * @param string                  $requestedName  Service name requested
     *
     * @return mixed
     */
    public function createServiceWithName(
        ServiceLocatorInterface $serviceLocator,
        $name,
        $requestedName
    ) {
        return $this($serviceLocator, $requestedName);
    }
}
