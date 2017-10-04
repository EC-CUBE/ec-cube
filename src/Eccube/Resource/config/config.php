<?php return [
    'eccube_install' => env('ECCUBE_INSTALL', 0),
    'auth_magic' => env('AUTH_MAGIC', null),
    'auth_type' => 'HMAC',
    'password_hash_algos' => 'sha256',
    'force_ssl' => env('FORCE_SSL', false),
    'admin_allow_hosts' => env('ADMIN_ALLOW_HOSTS', []),
    'cookie_lifetime' => env('COOKIE_LIFETIME', 0),
    'cookie_name' => env('COOKIE_NAME', 'eccube'),
    'locale' => env('LOCALE', 'ja'),
    'timezone' => env('TIMEZONE', 'Asia/Tokyo'),
    'currency' => env('CURRENCY', 'JPY'),
    'trusted_proxies_connection_only' => env('TRUSTED_PROXIES_CONNECTION_ONLY', false),
    'trusted_proxies' => env('TRUSTED_PROXIES', []),
    'vendor_psr4' => ['Acme\\', 'Acme/']
];
