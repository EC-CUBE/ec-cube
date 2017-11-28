<?php return [
    'database' => [
        'default' => env('ECCUBE_DB_DEFAULT', 'sqlite'),
        'sqlite' => [
            'driver' => 'pdo_sqlite',
            'path' => env('ECCUBE_DB_DATABASE'),
        ],
        'mysql' => [
            'driver' => 'pdo_mysql',
            'host' => env('ECCUBE_DB_HOST', '127.0.0.1'),
            'dbname' => env('ECCUBE_DB_DATABASE', 'eccube_db'),
            'port' => env('ECCUBE_DB_PORT', '3306'),
            'user' => env('ECCUBE_DB_USERNAME', 'eccube_user'),
            'password' => env('ECCUBE_DB_PASSWORD', 'password'),
            'charset' => env('ECCUBE_DB_CHARSET', 'utf8'),
            'defaultTableOptions' => [
                'collate' => env('ECCUBE_DB_COLLATE', 'utf8_general_ci'),
            ],
        ],
        'pgsql' => [
            'driver' => 'pdo_pgsql',
            'host' => env('ECCUBE_DB_HOST', '127.0.0.1'),
            'dbname' => env('ECCUBE_DB_DATABASE', 'ss_eccube_3x_b'),
            'port' => env('ECCUBE_DB_PORT', '5432'),
            'user' => env('ECCUBE_DB_USERNAME', 'postgres'),
            'password' => env('ECCUBE_DB_PASSWORD', 'root'),
            'charset' => env('ECCUBE_DB_CHARSET', 'utf8'),
        ],
    ],
];
