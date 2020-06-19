<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PamiModule\Event\ConnectingEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConnectingEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetClient(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new ConnectingEvent($client->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertFalse($event->isPropagationStopped());
    }

    public function testShouldSetStopPropagation(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new ConnectingEvent($client->reveal());
        $event->setPropagationStopped();

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertTrue($event->isPropagationStopped());
    }
}
