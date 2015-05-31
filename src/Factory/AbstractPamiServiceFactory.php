<?php

namespace PamiModule\Factory;

use Zend\ServiceManager\AbstractFactoryInterface;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceLocatorInterface;

class AbstractPamiServiceFactory implements AbstractFactoryInterface
{
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
        return false !== $this->getFactoryMapping($serviceLocator, $requestedName);
    }

    /**
     * Get the factoring map.
     *
     * @param ServiceLocatorInterface $serviceLocator Service locator
     * @param string                  $name           Service name
     *
     * @return bool|array
     */
    private function getFactoryMapping(ServiceLocatorInterface $serviceLocator, $name)
    {
        $matches = [];

        if (!preg_match('/^pami\.(?P<serviceType>[a-z0-9_]+)\.(?P<serviceName>[a-z0-9_]+)$/', $name, $matches)) {
            return false;
        }

        $config = $serviceLocator->get('Config');
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
        $mappings = $this->getFactoryMapping($serviceLocator, $requestedName);

        if (!$mappings) {
            throw new ServiceNotFoundException();
        }

        $factoryClass = $mappings['factoryClass'];
        /* @var $factory \PamiModule\Service\AbstractFactory */
        $factory = new $factoryClass($mappings['serviceName']);

        return $factory->createService($serviceLocator);
    }
}
