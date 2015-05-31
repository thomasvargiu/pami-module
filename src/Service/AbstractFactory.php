<?php

namespace PamiModule\Service;

use RuntimeException;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class AbstractFactory implements FactoryInterface
{
    /**
     * Service name
     *
     * @var string
     */
    protected $name;

    /**
     * Constructor
     *
     * @param string $name Service name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Gets options from configuration based on name.
     *
     * @param ServiceLocatorInterface $sl   Service locator
     * @param string                  $key  Service type
     * @param null|string             $name Service name
     *
     * @return \Zend\Stdlib\AbstractOptions
     *
     * @throws \RuntimeException
     */
    public function getOptions(ServiceLocatorInterface $sl, $key, $name = null)
    {
        if ($name === null) {
            $name = $this->getName();
        }

        if (!$this->hasOptions($sl, $key, $name)) {
            // @codeCoverageIgnoreStart
            throw new RuntimeException(
                sprintf(
                    'Options with name "%s" could not be found in "pami_module.%s".',
                    $name,
                    $key
                )
            );
            // @codeCoverageIgnoreEnd
        }

        $options = $sl->get('Config');
        $options = $options['pami_module'];
        $options = isset($options[$key][$name]) ? $options[$key][$name] : null;

        $optionsClass = $this->getOptionsClass();

        return new $optionsClass($options);
    }

    /**
     * Return if options exists in configuration
     *
     * @param ServiceLocatorInterface $sl   Service locator
     * @param string                  $key  Service type
     * @param string                  $name Service name
     *
     * @return bool
     */
    public function hasOptions(ServiceLocatorInterface $sl, $key, $name)
    {
        $options = $sl->get('Config');
        $options = $options['pami_module'];

        return isset($options[$key][$name]);
    }

    /**
     * Service name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the class name of the options associated with this factory.
     *
     * @abstract
     *
     * @return string
     */
    abstract public function getOptionsClass();
}
