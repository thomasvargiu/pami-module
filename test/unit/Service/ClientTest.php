<?php

namespace PamiModuleTest\Service;

use PAMI\Client\Exception\ClientException;
use PAMI\Client\IClient;
use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Service\Client;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\EventDispatcher\EventDispatcherInterface;
use PamiModule\Event;

/**
 * @covers \PamiModule\Service\Client
 */
class ClientTest extends TestCase
{
    use ProphecyTrait;

    private $connection;
    private $eventDispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->connection = $this->prophesize(IClient::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
    }

    private function getConnectedClient(): Client
    {
        $client = new Client($this->connection->reveal(), $this->eventDispatcher->reveal());

        $connectingEvent = new Event\ConnectingEvent($client);
        $connectedEvent = new Event\ConnectedEvent($client);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectingEvent::class)
        ))
            ->willReturn($connectingEvent);

        $this->connection->open()->shouldBeCalled();

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectedEvent::class)
        ))
            ->willReturn($connectedEvent);

        $client->connect();

        return $client;
    }

    public function testShouldConnect(): void
    {
        $connection = $this->prophesize(IClient::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $client = new Client($connection->reveal(), $eventDispatcher->reveal());

        $connectingEvent = new Event\ConnectingEvent($client);
        $connectedEvent = new Event\ConnectedEvent($client);

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($connectingEvent);

        $connection->open()->shouldBeCalledOnce();

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectedEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($connectedEvent);

        $client->connect();
    }

    public function testShouldNotConnectWhenAlreadyConnected(): void
    {
        $connection = $this->prophesize(IClient::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $client = new Client($connection->reveal(), $eventDispatcher->reveal());

        $connectingEvent = new Event\ConnectingEvent($client);
        $connectedEvent = new Event\ConnectedEvent($client);

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($connectingEvent);

        $connection->open()->shouldBeCalledOnce();

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectedEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($connectedEvent);

        $client->connect();
        $client->connect();
    }

    public function testShouldNotConnectWhenEventStopPropagation(): void
    {
        $connection = $this->prophesize(IClient::class);
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $client = new Client($connection->reveal(), $eventDispatcher->reveal());

        $connectingEvent = new Event\ConnectingEvent($client);
        $connectingEvent->setPropagationStopped();
        $connectedEvent = new Event\ConnectedEvent($client);

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($connectingEvent);

        $connection->open()->shouldNotBeCalled();

        $eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ConnectedEvent::class)
        ))
            ->shouldNotBeCalled()
            ->willReturn($connectedEvent);

        $client->connect();
    }

    public function testShouldDisconnect(): void
    {
        $client = $this->getConnectedClient();

        $disconnectingEvent = new Event\DisconnectingEvent($client);
        $disconnectedEvent = new Event\DisconnectedEvent($client);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($disconnectingEvent);

        $this->connection->close()->shouldBeCalledOnce();

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectedEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($disconnectedEvent);

        $client->disconnect();
    }

    public function testShouldNotDisconnectWhenNotConnected(): void
    {
        $client = $this->getConnectedClient();

        $disconnectingEvent = new Event\DisconnectingEvent($client);
        $disconnectedEvent = new Event\DisconnectedEvent($client);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($disconnectingEvent);

        $this->connection->close()->shouldBeCalledOnce();

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectedEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($disconnectedEvent);

        $client->disconnect();
        $client->disconnect();
    }

    public function testShouldNotDisconnectWhenEventStopPropagation(): void
    {
        $client = $this->getConnectedClient();

        $disconnectingEvent = new Event\DisconnectingEvent($client);
        $disconnectingEvent->setPropagationStopped();

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectingEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($disconnectingEvent);

        $this->connection->close()->shouldNotBeCalled();

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\DisconnectedEvent::class)
        ))
            ->shouldNotBeCalled();

        $client->disconnect();
    }

    public function testShouldProcess(): void
    {
        $client = $this->getConnectedClient();

        $this->connection->process()->shouldBeCalledOnce();

        $client->process();
    }

    public function testShouldReconnectAndProcessOnConnectionError(): void
    {
        $connection = $this->connection;
        $client = $this->getConnectedClient();

        $clientException = new ClientException('exception');

        $connection->open()->shouldBeCalledTimes(2);
        $connection->process()->shouldBeCalledTimes(2)->will(function () use ($clientException, $connection) {
            $connection->process()->shouldBeCalledTimes(2)->will(function () {});
            throw $clientException;
        });

        $client->process();
    }

    public function testShouldSendAction(): void
    {
        $client = $this->getConnectedClient();

        $action = $this->prophesize(OutgoingMessage::class);
        $response = $this->prophesize(ResponseMessage::class);

        $sendingActionEvent = new Event\SendingActionEvent($client, $action->reveal());
        $responseReceivedEvent = new Event\ResponseReceivedEvent($client, $action->reveal(), $response->reveal());

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\SendingActionEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($sendingActionEvent);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ResponseReceivedEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($responseReceivedEvent);

        $this->connection->send($action->reveal())
            ->shouldBeCalled()
            ->willReturn($response->reveal());

        $this->assertSame($response->reveal(), $client->sendAction($action->reveal()));
    }

    public function testShouldReconnectOnSendActionError(): void
    {
        $this->expectException(ClientException::class);
        $this->expectExceptionMessage('exception');

        $client = $this->getConnectedClient();

        $clientException = new ClientException('exception');
        $action = $this->prophesize(OutgoingMessage::class);
        $response = $this->prophesize(ResponseMessage::class);

        $sendingActionEvent = new Event\SendingActionEvent($client, $action->reveal());
        $responseReceivedEvent = new Event\ResponseReceivedEvent($client, $action->reveal(), $response->reveal());

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\SendingActionEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($sendingActionEvent);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ResponseReceivedEvent::class)
        ))
            ->shouldNotBeCalled()
            ->willReturn($responseReceivedEvent);

        $this->connection->open()->shouldBeCalledTimes(2);

        $this->connection->send($action->reveal())
            ->shouldBeCalled()
            ->willThrow($clientException);

        $client->sendAction($action->reveal());
    }

    public function testShouldNotSendActionWhenEventStopPropagation(): void
    {
        $client = $this->getConnectedClient();

        $action = $this->prophesize(OutgoingMessage::class);
        $response = $this->prophesize(ResponseMessage::class);

        $sendingActionEvent = new Event\SendingActionEvent($client, $action->reveal());
        $sendingActionEvent->setResponse($response->reveal());

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\SendingActionEvent::class)
        ))
            ->shouldBeCalledOnce()
            ->willReturn($sendingActionEvent);

        $this->eventDispatcher->dispatch(Argument::allOf(
            Argument::type(Event\ResponseReceivedEvent::class)
        ))
            ->shouldNotBeCalled();

        $this->connection->send($action->reveal())
            ->shouldNotBeCalled();

        $client->sendAction($action->reveal());
    }
}
