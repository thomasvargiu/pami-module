<?php

declare(strict_types=1);

namespace PamiModule\Event;

use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Service\ClientInterface;
use Psr\EventDispatcher\StoppableEventInterface;

class SendingActionEvent implements StoppableEventInterface
{
    private ClientInterface $client;

    private OutgoingMessage $action;

    private ?ResponseMessage $response;

    private bool $stopped = false;

    public function __construct(ClientInterface $client, OutgoingMessage $action)
    {
        $this->client = $client;
        $this->action = $action;
        $this->response = null;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function getAction(): OutgoingMessage
    {
        return $this->action;
    }

    public function getResponse(): ?ResponseMessage
    {
        return $this->response;
    }

    public function isPropagationStopped(): bool
    {
        return $this->stopped;
    }

    public function setResponse(ResponseMessage $response): void
    {
        $this->response = $response;
        $this->stopped = true;
    }
}
