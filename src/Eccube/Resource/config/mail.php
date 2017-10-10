<?php return [
    'mail' => [
        'transport' => env('ECCUBE_MAIL_TRANSPORT', 'smtp'),
        'host' => env('ECCUBE_MAIL_HOST', '127.0.0.1'),
        'port' => env('ECCUBE_MAIL_PORT', '1025'),
        'username' => env('ECCUBE_MAIL_USERNAME'),
        'password' => env('ECCUBE_MAIL_PASSWORD'),
        'encryption' => env('ECCUBE_MAIL_ENCRYPTION'),
        'auth_mode' => env('ECCUBE_MAIL_AUTH_MODE'),
        'charset_iso_2022_jp' => env('ECCUBE_MAIL_CHARSET_ISO_2022_JP', false),
        'spool' => env('ECCUBE_MAIL_SPOOL', false),
    ],
];
