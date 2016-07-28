<?php

namespace PamiModule\Options;

use Zend\Stdlib\AbstractOptions;

/**
 * Class Connection
 *
 * @package PamiModule\Options
 */
class Connection extends AbstractOptions
{
    const SCHEME_TCP = 'tcp://';

    /**
     * Hostname.
     *
     * @var string
     */
    protected $host;
    /**
     * Connection scheme.
     *
     * @var string
     */
    protected $scheme = 'tcp://';
    /**
     * Connection port.
     *
     * @var int
     */
    protected $port = 5038;
    /**
     * Username.
     *
     * @var string
     */
    protected $username;
    /**
     * Password.
     *
     * @var string
     */
    protected $secret;
    /**
     * Connection timeout.
     *
     * @var int
     */
    protected $connectTimeout = 10000;
    /**
     * Read timeout.
     *
     * @var int
     */
    protected $readTimeout = 10000;

    /**
     * Return the host.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set the host.
     *
     * @param string $host IP address or hostname
     *
     * @return $this
     */
    public function setHost($host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * Return the connection scheme.
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Set the connection scheme.
     *
     * @param string $scheme Connection scheme
     *
     * @return $this
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

        return $this;
    }

    /**
     * Return the connection port.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set the connection port.
     *
     * @param int $port Connection port
     *
     * @return $this
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }

    /**
     * Get the username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set the username.
     *
     * @param string $username Username
     *
     * @return $this
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Return the password.
     *
     * @return string
     */
    public function getSecret()
    {
        return $this->secret;
    }

    /**
     * Set the password.
     *
     * @param string $secret Password
     *
     * @return $this
     */
    public function setSecret($secret)
    {
        $this->secret = $secret;

        return $this;
    }

    /**
     * Get the connection timeout in ms.
     *
     * @return int
     */
    public function getConnectTimeout()
    {
        return $this->connectTimeout;
    }

    /**
     * Set the connection timeout.
     *
     * @param int $connectTimeout Connection timeout in ms
     *
     * @return $this
     */
    public function setConnectTimeout($connectTimeout)
    {
        $this->connectTimeout = (int) $connectTimeout;

        return $this;
    }

    /**
     * Return the read timeout in ms.
     *
     * @return int
     */
    public function getReadTimeout()
    {
        return $this->readTimeout;
    }

    /**
     * Set the read timeout.
     *
     * @param int $readTimeout Read timeout in ms
     *
     * @return $this
     */
    public function setReadTimeout($readTimeout)
    {
        $this->readTimeout = (int) $readTimeout;

        return $this;
    }
}
