<?php

namespace PamiModule\Options;

use Zend\Stdlib\AbstractOptions;

class Client extends AbstractOptions
{
    /**
     * @var string
     */
    protected $connection;
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @return string
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $connection
     *
     * @return $this
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param array $params
     *
     * @return $this
     */
    public function setParams(array $params)
    {
        $this->params = $params;

        return $this;
    }
}
