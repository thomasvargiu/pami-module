<?php

namespace PamiModuleTest;

use PamiModule\Factory\AbstractPamiServiceFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\Util\ModuleLoader;

class AbstractPamiServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ModuleLoader
     */
    protected $moduleLoader;

    protected function setUp()
    {
        parent::setUp();
        $this->moduleLoader = new ModuleLoader(include __DIR__.'/../../TestConfiguration.php.dist');
    }

    public function testCanCreateServiceWithName()
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
                ],
            ],
        ];

        $serviceManager = $this->moduleLoader->getServiceManager();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('Config');
        $config = ArrayUtils::merge($config, $configuration);
        $serviceManager->setService('Config', $config);

        $serviceFactory = new AbstractPamiServiceFactory();

        // Test creating client
        static::assertFalse($serviceFactory->canCreateServiceWithName(
            $serviceManager,
            'pami.connection.foo',
            'pami.connection.foo'
        ));
        static::assertFalse($serviceFactory->canCreateServiceWithName(
            $serviceManager,
            'foo.client.default',
            'foo.client.default'
        ));
        static::assertTrue($serviceFactory->canCreateServiceWithName(
            $serviceManager,
            'pami.connection.default',
            'pami.connection.default'
        ));
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
                ],
            ],
        ];

        $serviceManager = $this->moduleLoader->getServiceManager();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('Config');
        $config = ArrayUtils::merge($config, $configuration);
        $serviceManager->setService('Config', $config);

        $serviceFactory = new AbstractPamiServiceFactory();

        // Test creating connection

        $service = $serviceFactory->createServiceWithName(
            $serviceManager,
            'pami.connection.default',
            'pami.connection.default'
        );
        static::assertInstanceOf(
            'PAMI\\Client\\Impl\\ClientImpl',
            $service
        );
    }

    /**
     * @expectedException \Zend\ServiceManager\Exception\ServiceNotFoundException
     */
    public function testCreateServiceWithNameAndInvalidService()
    {
        $serviceManager = $this->moduleLoader->getServiceManager();

        $serviceFactory = new AbstractPamiServiceFactory();

        // Test creating client

        $serviceFactory->createServiceWithName(
            $serviceManager,
            'pami.foo.default',
            'pami.foo.default'
        );
    }
}
