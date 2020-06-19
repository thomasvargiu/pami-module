<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Event\ResponseReceivedEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class ResponseReceivedEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetProps(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $message = $this->prophesize(OutgoingMessage::class);
        $response = $this->prophesize(ResponseMessage::class);

        $event = new ResponseReceivedEvent($client->reveal(), $message->reveal(), $response->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertSame($message->reveal(), $event->getAction());
        $this->assertSame($response->reveal(), $event->getResponse());
    }
}
