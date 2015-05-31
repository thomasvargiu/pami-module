<?php

namespace PamiModule\Options;

use Zend\Stdlib\AbstractOptions;

class Connection extends AbstractOptions
{
    const SCHEME_TCP = 'tcp://';

    /**
     * @var string
     */
    protected $host;
    /**
     * @var string
     */
    protected $scheme = 'tcp://';
    /**
     * @var int
     */
    protected $port = 5038;
    /**
     * @var string
     */
    protected $username;
    /**
     * @var string
     */
    protected $secret;
    /**
     * @var int
     */
    protected $connectTimeout = 10000;
    /**
     * @var int
     */
    protected $readTimeout = 10000;

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param string $scheme
     *
     * @return $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param int $port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * @param string $secret
     *
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * @param int $connectTimeout
     *
     * @return $this
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = (int) $connectTimeout;

        return $this;
    }

    /**
     * @return int
     */
    public function getReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * @param int $readTimeout
     *
     * @return $this
     */
    public function setReadTimeout($readTimeout)
    {
        $this->readTimeout = (int) $readTimeout;

        return $this;
    }
}
