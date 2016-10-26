<?php

$modules = [
    'PamiModule',
];

if (class_exists('Zend\Router\Module')) {
    $modules[] = 'Zend\Router';
}

return [
    'modules' => $modules,
    'module_listener_options' => [
        'config_glob_paths' => [],
        'module_paths' => [],
    ],
];
