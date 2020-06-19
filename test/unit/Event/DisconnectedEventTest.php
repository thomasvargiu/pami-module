<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PamiModule\Event\DisconnectedEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class DisconnectedEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetClient(): void
    {
        $client = $this->prophesize(ClientInterface::class);

        $event = new DisconnectedEvent($client->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
    }
}
