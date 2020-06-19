<?php

declare(strict_types=1);

namespace PamiModule\Service;

use PAMI\Client\Exception\ClientException;
use PAMI\Client\IClient;
use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Event;
use Psr\EventDispatcher\EventDispatcherInterface;

class Client implements ClientInterface
{
    private IClient $connection;

    private EventDispatcherInterface $eventDispatcher;

    private bool $connected = false;

    public function __construct(IClient $connection, EventDispatcherInterface $eventDispatcher)
    {
        $this->connection = $connection;
        $this->eventDispatcher = $eventDispatcher;
        $this->connection->registerEventListener(new EventForwarder($this, $eventDispatcher));
    }

    /**
     * Return the PAMI client.
     *
     * @return IClient
     */
    public function getConnection(): IClient
    {
        return $this->connection;
    }

    /**
     * Connect to the Asterisk Manager Interface.
     *
     * @throws ClientException
     */
    public function connect(): void
    {
        if ($this->connected) {
            return;
        }

        /** @var Event\ConnectingEvent $event */
        $event = $this->eventDispatcher->dispatch(new Event\ConnectingEvent($this));

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->connection->open();
        $this->connected = true;

        $this->eventDispatcher->dispatch(new Event\ConnectedEvent($this));
    }

    /**
     * Disconnect from the Asterisk Manager Interface.
     */
    public function disconnect(): void
    {
        if (! $this->connected) {
            return;
        }

        /** @var Event\ConnectingEvent $event */
        $event = $this->eventDispatcher->dispatch(new Event\DisconnectingEvent($this));

        if ($event->isPropagationStopped()) {
            return;
        }

        $this->connected = false;
        $this->connection->close();

        $this->eventDispatcher->dispatch(new Event\DisconnectedEvent($this));
    }

    /**
     * Main processing loop. Also called from send(), you should call this in
     * your own application in order to continue reading events and responses
     * from ami.
     */
    public function process(): void
    {
        try {
            $this->connection->process();
        } catch (ClientException $e) {
            $this->reconnect();
            $this->connection->process();
        }
    }

    private function reconnect(): void
    {
        $this->connected = false;
        $this->connect();
    }

    /**
     * Sends a message to AMI.
     *
     * @param OutgoingMessage $action
     * @return ResponseMessage
     * @throws ClientException
     */
    public function sendAction(OutgoingMessage $action): ResponseMessage
    {
        /** @var Event\SendingActionEvent $event */
        $event = $this->eventDispatcher->dispatch(new Event\SendingActionEvent($this, $action));

        if ($event->isPropagationStopped()) {
            $response = $event->getResponse();

            if (null === $response) {
                throw new \RuntimeException('Action stopped without a response');
            }

            return $response;
        }

        try {
            $response = $this->connection->send($action);
        } catch (ClientException $e) {
            $this->reconnect();
            throw $e;
        }

        $this->eventDispatcher->dispatch(new Event\ResponseReceivedEvent($this, $action, $response));

        return $response;
    }
}
