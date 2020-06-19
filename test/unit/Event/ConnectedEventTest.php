<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PamiModule\Event\ConnectedEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ConnectedEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetClient(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new ConnectedEvent($client->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
    }
}
