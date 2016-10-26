<?php

namespace PamiModuleTest;

use PamiModule\ConfigProvider;
use Zend\ServiceManager\Factory\InvokableFactory;

class ConfigProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testProviderInvoke()
    {
        $expectedDependencies = [
            'factories' => [
                \PamiModule\Listener\ConnectionStatusListener::class => InvokableFactory::class,
            ],
            'abstract_factories' => [
                \PamiModule\Factory\AbstractPamiServiceFactory::class,
            ],
            'shared' => [
                \PamiModule\Listener\ConnectionStatusListener::class => false,
            ],
        ];

        $provider = new ConfigProvider();
        $config = $provider();

        $this->assertArrayHasKey('dependencies', $config);
        $dependencies = $config['dependencies'];

        $this->assertSame($expectedDependencies, $dependencies);
    }
}
