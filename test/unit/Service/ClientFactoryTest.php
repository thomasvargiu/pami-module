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
        $this->moduleLoader = new ModuleLoader(include __DIR__.'/../../TestConfiguration.php.dist');
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

        $connectionMock = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->getMock();

        $serviceManager = $this->moduleLoader->getServiceManager();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('Config');
        $config = ArrayUtils::merge($config, $configuration);
        $serviceManager->setService('Config', $config);
        $serviceManager->setService('pami.connection.default', $connectionMock);

        $factory = new ClientFactory('default');
        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('PamiModule\\Service\\Client', $service);
        static::assertEquals(['foo' => 'bar'], $service->getParams());
    }
}
