<?php

namespace PamiModuleTest\Service;

use PAMI\Message\Event\BridgeEvent;
use PamiModule\PamiEvent;
use PamiModule\Service\Client;

class ClientTest extends \PHPUnit_Framework_TestCase
{
    public function testClient()
    {
        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->getMock();

        $pami->expects(static::exactly(2))
            ->method('registerEventListener')
            ->willReturn('uniqueIdFoo');

        $pami->expects(static::once())
            ->method('unregisterEventListener')
            ->with('uniqueIdFoo');

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client($pami);
        static::assertSame($pami, $client->getConnection());

        // Test attach EventManager

        $eventManager = $client->getEventManager();
        static::assertInstanceOf('Zend\\EventManager\\EventManager', $eventManager);

        // Test unregister
        /** @var \Zend\EventManager\EventManager $eventManagerMock */
        $eventManagerMock = static::getMock('Zend\\EventManager\\EventManager');

        $client->setEventManager($eventManagerMock);
    }

    public function testOnConnectionEvent()
    {
        $eventManagerMock = static::getMock('Zend\\EventManager\\EventManager');

        $pami = static::getMockBuilder('PAMI\\Client\\Impl\\ClientImpl')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var \PAMI\Client\Impl\ClientImpl $pami */

        $client = new Client($pami);

        $incomingMessage = new BridgeEvent('Event: Bridge');

        $eventManagerMock->expects(static::once())
            ->method('trigger')
            ->with(static::callback(function (PamiEvent $e) use ($client, $incomingMessage) {
                static::assertInstanceOf('PamiModule\\PamiEvent', $e);
                static::assertSame($client, $e->getTarget());
                static::assertSame($incomingMessage, $e->getEvent());

                return true;
            }));

        $client->setEventManager($eventManagerMock);

        $method = new \ReflectionMethod('PamiModule\\Service\\Client', 'onConnectionEvent');
        $method->setAccessible(true);

        $method->invoke($client, $incomingMessage);
    }
}
