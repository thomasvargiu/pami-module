<?php

namespace PamiModuleTest\Event;

use PamiModule\Event\EventForwarder;
use PamiModule\Event\PamiEvent;

class EventForwarderTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $client = static::getMockBuilder('PamiModule\\Service\\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $pamiEvent = static::getMockBuilder('PAMI\\Message\\Event\\EventMessage')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $pamiEvent->expects(static::any())
            ->method('getName')
            ->willReturn('Foo');

        $eventManager = static::getMock('Zend\\EventManager\\EventManager', ['trigger']);

        $eventManager->expects(static::once())
            ->method('trigger')
            ->with(static::callback(function (PamiEvent $e) use ($client, $pamiEvent) {
                static::assertInstanceOf('PamiModule\\Event\\PamiEvent', $e);
                static::assertSame($client, $e->getTarget());
                static::assertSame($pamiEvent, $e->getEvent());
                static::assertEquals('event.Foo', $e->getName());

                return true;
            }));

        $client->expects(static::any())
            ->method('getEventManager')
            ->willReturn($eventManager);

        /* @var \PamiModule\Service\Client $client */
        /* @var \PAMI\Message\Event\EventMessage $pamiEvent */

        $eventForwarder = new EventForwarder($client);

        $eventForwarder->handle($pamiEvent);
    }
}
