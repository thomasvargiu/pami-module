<?php

namespace PamiModuleTest\Service;

use PamiModule\Listener\ConnectionStatusListener;
use PamiModule\Service\ConnectionStatusDelegatorFactory;

class ConnectionStatusDelegatorFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateDelegatorWithName()
    {
        $eventManager = $this->getMock('Zend\\EventManager\\EventManagerInterface');
        $client = $this->getMock('PamiModule\\Service\\Client', ['getEventManager'], [], '', false);
        $serviceLocator = $this->getMock('Zend\\ServiceManager\\ServiceManager');

        $client->expects(static::any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        $listener = $this->getMockBuilder(ConnectionStatusListener::class)
            ->disableOriginalConstructor()
            ->setMethods(['attach'])
            ->getMock();

        $serviceLocator->expects(static::once())
            ->method('get')
            ->with(ConnectionStatusListener::class)
            ->willReturn($listener);

        $listener->expects(static::once())
            ->method('attach')
            ->with($eventManager);

        $callback = function () use ($client) {
            return $client;
        };

        $delegatorFactory = new ConnectionStatusDelegatorFactory();
        $result = $delegatorFactory->createDelegatorWithName($serviceLocator, '', '', $callback);

        static::assertSame($client, $result);
    }
}
