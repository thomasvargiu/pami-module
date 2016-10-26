<?php

$finder = Symfony\CS\Finder::create();
$finder->in([
    __DIR__.'/src',
    __DIR__.'/test/unit',
]);
$finder->files()->name('*.php');

$config = Symfony\CS\Config::create();
$config->level(Symfony\CS\FixerInterface::SYMFONY_LEVEL);
$config->fixers([
        '-concat_without_spaces',
        'concat_with_spaces',
        'no_useless_else',
        'no_useless_return',
        'ordered_use',
        'php_unit_construct',
        'phpdoc_order',
        'short_array_syntax',
        'short_echo_tag',
    ]);

$config->finder($finder);
return $config;