<?php return [
    'database' => [
        'default' => env('DB_DEFAULT', 'sqlite'),
        'sqlite' => [
            'driver' => 'pdo_sqlite',
            'path' => env('DB_DATABASE'),
        ],
        'mysql' => [
            'driver' => 'pdo_mysql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'dbname' => env('DB_DATABASE', 'eccube_db'),
            'port' => env('DB_PORT', '3306'),
            'user' => env('DB_USERNAME', 'eccube_user'),
            'password' => env('DB_PASSWORD', 'password'),
            'charset' => env('DB_CHARSET', 'utf8'),
            'defaultTableOptions' => [
                'collate' => env('DB_COLLATE', 'utf8_general_ci'),
            ],
        ],
        'pgsql' => [
            'driver' => 'pdo_pgsql',
            'host' => env('DB_HOST', '127.0.0.1'),
            'dbname' => env('DB_DATABASE', 'eccube_db'),
            'port' => env('DB_PORT', '5432'),
            'user' => env('DB_USERNAME', 'eccube_user'),
            'password' => env('DB_PASSWORD', 'password'),
            'charset' => env('DB_CHARSET', 'utf8'),
        ],
    ],
];
