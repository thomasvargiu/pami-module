<?php

declare(strict_types=1);

namespace PamiModule;

/**
 * Class Module.
 */
class Module
{
    /**
     * Provide configuration for an application integrating PamiModule.
     *
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        $provider = new ConfigProvider();

        return [
            'pami' => [
                'connection' => [],
                'client' => [],
            ],
            'service_manager' => $provider->getDependencies(),
        ];
    }
}
