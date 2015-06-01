<?php

namespace PamiModuleTest\Service;

use PamiModule\Service\ConnectionStatusDelegatorFactory;

class ConnectionStatusDelegatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDelegatorWithName()
    {
        $eventManager = static::getMock('Zend\\EventManager\\EventManagerInterface');
        $client = static::getMock('PamiModule\\Service\\Client', ['getEventManager'], [], '', false);
        $serviceLocator = static::getMock('Zend\\ServiceManager\\ServiceLocatorInterface');

        $client->expects(static::any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $eventManager->expects(static::once())
            ->method('attachAggregate')
            ->with(static::isInstanceOf('PamiModule\\Listener\\ConnectionStatusListener'));

        $callback = function () use ($client) {
            return $client;
        };

        $delegatorFactory = new ConnectionStatusDelegatorFactory();
        $result = $delegatorFactory->createDelegatorWithName($serviceLocator, '', '', $callback);

        static::assertSame($client, $result);
    }
}
