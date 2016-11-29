<?php

namespace PamiModule\Service;

use Interop\Container\ContainerInterface;
use InvalidArgumentException;
use PAMI\Client\Impl\ClientImpl;
use PamiModule\Options\Connection as ConnectionOptions;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ConnectionFactory.
 */
class ConnectionFactory extends AbstractFactory
{
    /**
     * Get the class name of the options associated with this factory.
     *
     * @return string
     */
    public function getOptionsClass()
    {
        return \PamiModule\Options\Connection::class;
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return ClientImpl
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        /* @var $options ConnectionOptions */
        $options = $this->getOptions($container, 'connection');

        return $this->createConnection($options);
    }

    /**
     * Create service.
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ClientImpl
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, ClientImpl::class);
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
        $clientOptions = [
            'host' => $options->getHost(),
            'port' => $options->getPort(),
            'username' => $options->getUsername(),
            'secret' => $options->getSecret(),
            'connect_timeout' => $options->getConnectTimeout(),
            'read_timeout' => $options->getReadTimeout(),
            'scheme' => $options->getScheme(),
            'event_mask' => $options->getEventMask(),
        ];

        // Disable logging for version <2.0
        $clientOptions['log4php.properties'] = [
            'rootLogger' => [
                'appenders' => ['default'],
            ],
            'appenders' => [
                'default' => [
                    'class' => 'LoggerAppenderNull',
                ],
            ],
        ];

        return new ClientImpl($clientOptions);
    }
}
