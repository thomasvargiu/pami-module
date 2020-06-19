<?php

declare(strict_types=1);

namespace PamiModule\Service;

use PAMI\Client\Exception\ClientException;
use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;

interface ClientInterface
{
    /**
     * Connect to the Asterisk Manager Interface.
     *
     * @throws ClientException
     */
    public function connect(): void;

    /**
     * Disconnect from the Asterisk Manager Interface.
     */
    public function disconnect(): void;

    /**
     * Main processing loop. Also called from send(), you should call this in
     * your own application in order to continue reading events and responses
     * from ami.
     */
    public function process(): void;

    /**
     * Sends a message to AMI.
     *
     * @param OutgoingMessage $action
     * @return ResponseMessage
     * @throws ClientException
     */
    public function sendAction(OutgoingMessage $action): ResponseMessage;
}
