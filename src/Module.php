<?php

namespace PamiModule;

/**
 * Class Module.
 */
class Module
{
    /**
     * Provide configuration for an application integrating PamiModule.
     *
     * @return array
     */
    public function getConfig()
    {
        $provider = new ConfigProvider();

        return [
            'pami_module' => [
                'connection' => [],
                'client' => [],
            ],
            'pami_module_factories' => [
                'connection' => Service\ConnectionFactory::class,
                'client' => Service\ClientFactory::class,
            ],
            'service_manager' => $provider->getDependencyConfig(),
        ];
    }
}
