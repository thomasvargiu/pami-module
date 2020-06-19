<?php

declare(strict_types=1);

namespace PamiModuleTest\Event;

use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Event\SendingActionEvent;
use PamiModule\Service\ClientInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class SendingActionEventTest extends TestCase
{
    use ProphecyTrait;

    public function testShouldGetClient(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $message = $this->prophesize(OutgoingMessage::class);

        $event = new SendingActionEvent($client->reveal(), $message->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertSame($message->reveal(), $event->getAction());
        $this->assertFalse($event->isPropagationStopped());
        $this->assertNull($event->getResponse());
    }

    public function testShouldSetStopPropagation(): void
    {
        $client = $this->prophesize(ClientInterface::class);
        $message = $this->prophesize(OutgoingMessage::class);
        $response = $this->prophesize(ResponseMessage::class);

        $event = new SendingActionEvent($client->reveal(), $message->reveal());
        $event->setResponse($response->reveal());

        $this->assertSame($client->reveal(), $event->getClient());
        $this->assertSame($message->reveal(), $event->getAction());
        $this->assertTrue($event->isPropagationStopped());
        $this->assertSame($response->reveal(), $event->getResponse());
    }
}
