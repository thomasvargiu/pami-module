<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PAMI\Message\Event\EventMessage;
use PamiModule\Event\PamiEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class PamiEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetProps(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $eventMessage = $this->prophesize(EventMessage::class);
        $eventMessage->getName()->willReturn('foo');

        $event = new PamiEvent($client->reveal(), $eventMessage->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertSame($eventMessage->reveal(), $event->getEvent());
        $this->assertSame('foo', $event->getEventName());
    }
}
