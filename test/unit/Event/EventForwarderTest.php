<?php

namespace PamiModuleTest\Event;

use PamiModule\Event\EventForwarder;
use PamiModule\Event\PamiEvent;

class EventForwarderTest extends \PHPUnit_Framework_TestCase
{
    public function testHandle()
    {
        $client = $this->getMockBuilder('PamiModule\\Service\\Client')
            ->disableOriginalConstructor()
            ->getMock();

        $pamiEvent = $this->getMockBuilder('PAMI\\Message\\Event\\EventMessage')
            ->disableOriginalConstructor()
            ->setMethods(['getName'])
            ->getMock();

        $pamiEvent->expects(static::any())
            ->method('getName')
            ->willReturn('Foo');

        $eventManager = $this->getMockBuilder('Zend\\EventManager\\EventManager')
            ->setMethods(['triggerEvent'])
            ->getMock();

        $eventManager->expects(static::once())
            ->method('triggerEvent')
            ->with(static::callback(function (PamiEvent $e) use ($client, $pamiEvent) {
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
