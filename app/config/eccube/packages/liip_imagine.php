<?php

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $container->extension('liip_imagine', [
        'resolvers' => [
            'default' => [
                'web_path' => [
                    'web_root' => '%kernel.project_dir%',
                    'cache_prefix' => 'html/upload/cache'
                ]
            ]
        ],
        'loaders' => [
            'default' => [
                'filesystem' => [
                    'data_root' => $_SERVER['DOCUMENT_ROOT'],
                ]
            ]
        ],
        'filter_sets' => [
            'cache' => null,
            'resize' => []
        ]
    ]);
};
