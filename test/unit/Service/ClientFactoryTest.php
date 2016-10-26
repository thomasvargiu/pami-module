<?php

namespace PamiModuleTest\Service;

use PamiModule\Service\ClientFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\Util\ModuleLoader;

class ClientFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModuleLoader
     */
    protected $moduleLoader;

    protected function setUp()
    {
        parent::setUp();
        $this->moduleLoader = new ModuleLoader(include __DIR__ . '/../../TestConfiguration.php');
    }

    public function testCreateService()
    {
        $configuration = [
            'pami_module' => [
                'connection' => [
                    'default' => [
                        'host' => 'local.host',
                        'scheme' => 'tcp://',
                        'port' => 123,
                        'username' => 'admin',
                        'secret' => 'foosecret',
                        'connect_timeout' => 123,
                        'read_timeout' => 123,
                    ],
                    'other' => [
                        'host' => 'local2.host',
                        'scheme' => 'tcp://',
                        'port' => 123,
                        'username' => 'admin',
                        'secret' => 'foosecret',
                        'connect_timeout' => 123,
                        'read_timeout' => 123,
                    ],
                ],
                'client' => [
                    'default' => [
                        'connection' => 'default',
                        'params' => [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ];

        $connectionMock = $this->getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->setMethods(['registerEventListener'])
            ->getMock();

        $connectionMock->expects(static::once())
            ->method('registerEventListener')
            ->with(static::isInstanceOf('PamiModule\\Event\\EventForwarder'));

        $serviceManager = $this->moduleLoader->getServiceManager();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('config');
        $config = ArrayUtils::merge($config, $configuration);
        $serviceManager->setService('config', $config);
        $serviceManager->setService('pami.connection.default', $connectionMock);

        $factory = new ClientFactory('default');
        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('PamiModule\\Service\\Client', $service);
        static::assertEquals(['foo' => 'bar'], $service->getParams());
        static::assertInstanceOf('Zend\\EventManager\\EventManager', $service->getEventManager());

        // Test with client not in in configuration

        $factory = new ClientFactory('other');
        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('PamiModule\\Service\\Client', $service);
        static::assertEquals([], $service->getParams());
        static::assertInstanceOf('Zend\\EventManager\\EventManager', $service->getEventManager());
    }
}
