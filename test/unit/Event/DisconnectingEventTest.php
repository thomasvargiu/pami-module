<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PamiModule\Event\DisconnectingEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DisconnectingEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetClient(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new DisconnectingEvent($client->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testShouldSetStopPropagation(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new DisconnectingEvent($client->reveal());
        $event->setPropagationStopped();

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertTrue($event->isPropagationStopped());
    }
}
