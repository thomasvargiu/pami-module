<?php

namespace PamiModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class Client.
 */
class Client extends AbstractOptions
{
    /**
     * Connection name.
     *
     * @var string
     */
    protected $connection;
    /**
     * Custom params.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Connection name.
     *
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Set the connection name.
     *
     * @param string $connection Connection name
     *
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Custom params.
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
}
