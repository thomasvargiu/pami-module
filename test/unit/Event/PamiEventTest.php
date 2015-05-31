<?php

namespace PamiModuleTest\Event;

use PamiModule\Event\PamiEvent;

class PamiEventTest extends \PHPUnit_Framework_TestCase
{
    public function testMethods()
    {
        $event = new PamiEvent();

        $pamiEvent = static::getMockBuilder('PAMI\\Message\\Event\\EventMessage')
            ->disableOriginalConstructor()
            ->getMock();

        /* @var \PAMI\Message\Event\EventMessage $pamiEvent */

        $event->setEvent($pamiEvent);
        static::assertSame($pamiEvent, $event->getEvent());
    }
}
