<?php

namespace PamiModuleTest\Service;

use PamiModule\Service\ConnectionFactory;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\Util\ModuleLoader;

class ConnectionFactoryTest extends \PHPUnit_Framework_TestCase
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
            ],
        ];

        $serviceManager = $this->moduleLoader->getServiceManager();
        $serviceManager->setAllowOverride(true);

        $config = $serviceManager->get('config');
        $config = ArrayUtils::merge($config, $configuration);
        $serviceManager->setService('config', $config);

        $factory = new ConnectionFactory('default');
        $service = $factory->createService($serviceManager);

        static::assertInstanceOf('PAMI\\Client\\Impl\\ClientImpl', $service);
    }
}
