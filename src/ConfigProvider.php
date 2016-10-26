<?php

namespace PamiModule;

use Zend\ServiceManager\Factory\InvokableFactory;

/**
 * Class ConfigProvider.
 */
class ConfigProvider
{
    /**
     * Provide dependency configuration for an application integrating i18n.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencyConfig(),
        ];
    }
    /**
     * Provide dependency configuration for an application integrating i18n.
     *
     * @return array
     */
    public function getDependencyConfig()
    {
        return [
            'factories' => [
                Listener\ConnectionStatusListener::class => InvokableFactory::class,
            ],
            'abstract_factories' => [
                Factory\AbstractPamiServiceFactory::class,
            ],
            'shared' => [
                Listener\ConnectionStatusListener::class => false,
            ],
        ];
    }
}
