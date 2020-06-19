<?php

declare(strict_types=1);

namespace PamiModule\Event;

use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use PamiModule\Service\ClientInterface;

class ResponseReceivedEvent
{
    private ClientInterface $client;
    private OutgoingMessage $action;
    private ResponseMessage $response;

    public function __construct(ClientInterface $client, OutgoingMessage $action, ResponseMessage $response)
    {
        $this->client = $client;
        $this->action = $action;
        $this->response = $response;
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }

    public function getAction(): OutgoingMessage
    {
        return $this->action;
    }

    public function getResponse(): ResponseMessage
    {
        return $this->response;
    }
}
