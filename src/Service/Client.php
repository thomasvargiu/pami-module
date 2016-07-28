<?php

namespace PamiModule\Service;

use PAMI\Client\Impl\ClientImpl;
use PAMI\Message\OutgoingMessage;
use PAMI\Message\Response\ResponseMessage;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\EventManager\EventsCapableInterface;
use ArrayObject;

/**
 * Class Client
 *
 * @package PamiModule\Service
 */
class Client implements EventsCapableInterface
{
    use EventManagerAwareTrait;

    /**
     * PAMI client.
     *
     * @var ClientImpl
     */
    protected $connection;
    /**
     * Custom parameters.
     *
     * @var array
     */
    protected $params = [];
    /**
     * @var string
     */
    protected $host;

    /**
     * Client constructor.
     *
     * @param string                $host
     * @param ClientImpl            $pami         PAMI client
     */
    public function __construct($host, ClientImpl $pami)
    {
        $this->host = $host;
        $this->connection = $pami;
    }

    /**
     * Return the hostname of the connection.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Return the PAMI client.
     *
     * @return ClientImpl
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Return the custom parameters.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set the custom parameters.
     *
     * @param array $params Parameters
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Connect to the Asterisk Manager Interface.
     *
     * @return $this
     *
     * @throws \PAMI\Client\Exception\ClientException
     */
    public function connect()
    {
        $results = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);
        if ($results->stopped()) {
            return $this;
        }

        $this->connection->open();

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this);

        return $this;
    }

    /**
     * Disconnect from the Asterisk Manager Interface.
     *
     * @return $this
     */
    public function disconnect()
    {
        $results = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);
        if ($results->stopped()) {
            return $this;
        }

        $this->connection->close();

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this);

        return $this;
    }

    /**
     * Main processing loop. Also called from send(), you should call this in
     * your own application in order to continue reading events and responses
     * from ami.
     *
     * @return $this
     */
    public function process()
    {
        $results = $this->getEventManager()->trigger(__FUNCTION__.'.pre', $this);

        if ($results->stopped()) {
            return $this;
        }

        $this->connection->process();

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this);

        return $this;
    }

    /**
     * Sends a message to AMI.
     *
     * @param OutgoingMessage $action Action to send
     *
     * @return \PAMI\Message\Response\ResponseMessage
     *
     * @throws \PAMI\Client\Exception\ClientException
     */
    public function sendAction(OutgoingMessage $action)
    {
        $params = new ArrayObject(['action' => $action]);
        $results = $this->getEventManager()->triggerUntil(
            function ($response) {
                return $response instanceof ResponseMessage;
            },
            __FUNCTION__.'.pre',
            $this,
            $params
        );
        if ($results->stopped()) {
            return $results->last();
        }

        $response = $this->connection->send($action);

        $params['response'] = $response;
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, $params);

        return $response;
    }
}
