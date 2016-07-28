<?php

namespace PamiModule;

use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'pami_module' => [
        'connection' => [],
        'client' => []
    ],
    'pami_module_factories' => [
        'connection' => Service\ConnectionFactory::class,
        'client' => Service\ClientFactory::class,
    ],
    'service_manager' => [
        'factories' => [
            Listener\ConnectionStatusListener::class => InvokableFactory::class,
        ],
        'abstract_factories' => [
            Factory\AbstractPamiServiceFactory::class,
        ]
    ]
];
