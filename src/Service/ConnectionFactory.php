<?php

namespace PamiModule\Service;

use InvalidArgumentException;
use PAMI\Client\Impl\ClientImpl;
use PamiModule\Options\Connection as ConnectionOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

class ConnectionFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return 'PamiModule\\Options\\Connection';
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /* @var $options ConnectionOptions */
        $options = $this->getOptions($serviceLocator, 'connection');

        return $this->createConnection($options);
    }

    /**
     * @param ConnectionOptions $options
     *
     * @throws InvalidArgumentException
     *
     * @return ClientImpl
     */
    protected function createConnection(ConnectionOptions $options)
    {
        // @todo: implement logger
        $clientOptions = [
            'host' => $options->getHost(),
            'port' => $options->getPort(),
            'username' => $options->getUsername(),
            'secret' => $options->getSecret(),
            'connect_timeout' => $options->getConnectTimeout(),
            'read_timeout' => $options->getReadTimeout(),
            'scheme' => $options->getScheme(),
        ];

        /** @var ClientImpl $pami */
        $pami = new ClientImpl($clientOptions);

        return $pami;
    }
}
