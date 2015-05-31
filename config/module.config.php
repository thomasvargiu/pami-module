<?php
return [
    'pami_module' => [
        'connection' => [],
        'client' => []
    ],
    'pami_module_factories' => [
        'connection' => 'PamiModule\\Service\\ConnectionFactory',
        'client' => 'PamiModule\\Service\\ClientFactory'
    ],
    'service_manager' => [
        'abstract_factories' => [
            'PamiModule\\Factory\\AbstractPamiServiceFactory'
        ]
    ]
];
